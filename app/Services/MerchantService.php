<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        // TODO: Complete this method
        $user = User::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'email_verified_at' => now(),
            'password' => $data["api_key"],
            'remember_token' => Str::random(10),
            'type' => User::TYPE_MERCHANT
        ]);
        return $user->merchant()->create(["user_id" => $user->id, "domain" => $data["domain"], "display_name" => $data["name"]]);
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        // TODO: Complete this method
        $user->email=$data["email"];
        $user->name=$data["name"];
        $user->save();
        return Merchant::where("user_id", $user->id)->update([
            'domain' => $data["domain"],
            'display_name'=>$data['name']
        ]);
    }

    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        return Merchant::whereHas("user", function ($q) use ($email) {
            $q->where("email", $email);
        })->first();
    }

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
        $unpaidOrders = Order::where('affiliate_id', $affiliate->id)
                    ->where('payout_status', Order::STATUS_UNPAID)
                    ->get();
        
        foreach ($unpaidOrders as $order) {
            dispatch(new PayoutOrderJob($order));
        }
    }
}
