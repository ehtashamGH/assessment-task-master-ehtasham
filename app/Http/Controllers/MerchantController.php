<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class MerchantController extends Controller
{
    public function __construct(MerchantService $merchantService)
    {
    }

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method
        $orders = new Order();
        if ($request->has("to") && $request->has("from")) {
            $orders = $orders->whereBetween('created_at', [$request->get("from"), $request->get("to")]);
        }

        if (!empty(auth()->user()->id)) {
            $user_id = auth()->user()->id;
            $orders = $orders->whereHas("merchant", function ($q) use ($user_id) {
                $q->where("user_id", $user_id);
            });
        }

        $count = $orders->count();
        $revenue = $orders->sum("subtotal");
        $commission = $orders->sum("commission_owed");

        $noAffiliate_commision = $orders->whereNull("affiliate_id")->sum("commission_owed");

        $commissions_owed = $commission - $noAffiliate_commision;

        return response()->json(["count" => $count, "revenue" => $revenue, 'commissions_owed' => $commissions_owed]);
    }
}
