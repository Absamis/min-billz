<?php

namespace App\Jobs\Gsubz;

use App\Enums\AppEnums;
use App\Models\Billings\VTU\DataBundlePlan;
use App\Models\Billings\VTU\DataBundleService;
use App\Services\Apis\VTU\GsubzApiService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchDataPlans
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $gsubzService;
    public function __construct()
    {
        try {
            $this->gsubzService = new GsubzApiService(
                config("services.vtu.gsubz.api_url"),
                null,
                config("services.vtu.gsubz.api_key")
            );
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        $appCode = config("services.vtu.gsubz.app_code");
        echo "Start Fetch Data ".$appCode;
        try{
            $services = DataBundleService::active()->where("vendor", $appCode)->get();
        }catch(Exception $ex){
            echo $ex->getMessage();
        }
        foreach($services as $service){
            try{
                $resp = $this->gsubzService->getDataPlans($service->code);
                if(count($resp) == 0){
                    // $service->status = AppEnums::inactive;
                    // $service->save();
                    continue;
                }
                $plans = $resp["plans"] ?? false;
                if(!$plans){
                    // $service->status = AppEnums::inactive;
                    // $service->save();
                    continue;
                }
                $service->remarks = $resp["service"] ?? null;
                $service->save();

                $service->plans()->update(["status" => AppEnums::inactive]);
                foreach($plans as $plan){
                    $price = round($plan["price"] + ((appSettings()->data_plan_percent_increase / 100) * $plan["price"]));
                    DataBundlePlan::updateOrCreate([
                        "service_id" => $service->id,
                        "service_plan_id" => $plan["value"]
                    ], [
                        "name" => $plan["displayName"],
                        "vendor_price" => $plan["price"],
                        "price" => $price,
                        "service_name" => $service->code,
                        "vendor" => $appCode,
                        "status" => AppEnums::active
                    ]);
                }
            }catch(Exception $ex){

            }
        }
    }
}
