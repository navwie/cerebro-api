<?php

namespace Tests\Unit\Http\Controllers\Api\v1;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\CustomerToBank;
use App\Models\Reapply;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Tests\TestCase;

class ReapplyControllerTest extends TestCase
{

    protected $reapplyData;
    protected $user;
    public function setUp(): void
    {
        parent::setUp();
        config(['database.connections.mysql.database' => 'cerebro_test']);
        config(['database.connections.mysql_audit.database' => 'cerebro_audit_test']);
        config(['app.env' => 'testing']);
        $this->artisan('migrate:refresh', [
            '--force' => true,
        ]);
        $this->artisan('db:seed', [
            '--force' => true,
            '--class' => 'UsersAndNotesSeeder'
        ]);
        $this->artisan('cache:clear');
        $this->user = User::factory()->create([
            'post_back_amount' => config('dnm.defaultFormSettings.postBackAmount'),
            'personal_min_req' => config('dnm.defaultFormSettings.personalMinReq'),
            'personal_channel_id' => config('dnm.credentials.personalChannelId'),
            'personal_password' => config('dnm.credentials.personalPassword'),
            'lead_channel_id' => config('dnm.credentials.leadChannelId'),
            'lead_password' => config('dnm.credentials.leadPassword'),
        ]);
        $this->reapplyData = $this->create_data();
    }

    protected function create_data()
    {
        $customer = Customer::factory()->test()->make();
        $bank = Bank::factory()->make();
        $customerToBank = CustomerToBank::factory()
            ->for($bank)
            ->for($customer)
            ->make();

        $reapply = Reapply::factory()
            ->for($customer)
            ->test()
            ->make();
        $fullData = array_merge($reapply->toArray(), $customer->toArray(), $bank->toArray(), $customerToBank->toArray());

        $fullData['dob'] = Carbon::createFromFormat('Y-m-d', $fullData['dob'])->format('m-d-Y');
        $fullData['next_pay_day'] = Carbon::now()->format('m-d-Y');
        $fullData['second_pay_day'] = Carbon::now()->addDay()->format('m-d-Y');
        return $fullData;
    }

    public function test_ssn_check_update_main()
    {
        $reapplyData = $this->reapplyData;
        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);
        $reapplyData['ssn'] += 1;
        for ($i = 0; $i < config('dnm.ssnMiddleware.maxAttempts'); $i++) {
            $response = $this->actingAs($this->user)->json('post', '/api/v1/update_main', $reapplyData);
            $response->assertStatus(422);
        }

        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_main', $reapplyData);
        $response->assertStatus(429);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/search_reapply', $reapplyData);
        $response->assertStatus(429);
    }

    public function test_ssn_check_update_reapply()
    {
        $reapplyData = $this->reapplyData;
        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);
        $last_4_digits_ssn = substr($reapplyData['ssn'], - 4) + 1;
        $reapplyData['ssn'] = $last_4_digits_ssn;
        for ($i = 0; $i < config('dnm.ssnMiddleware.maxAttempts'); $i++) {
            $response = $this->actingAs($this->user)->json('post', '/api/v1/update_reapply', $reapplyData);
            $response->assertStatus(422);
        }
        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_reapply', $reapplyData);
        $response->assertStatus(429);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/search_reapply', $reapplyData);
        $response->assertStatus(429);
    }

    public function test_ssn_check_search_main()
    {
        $reapplyData = $this->reapplyData;
        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);
        $last_4_digits_ssn = substr($reapplyData['ssn'], - 4) + 1;
        $reapplyData['ssn'] = $last_4_digits_ssn;
        for ($i = 0; $i < config('dnm.ssnMiddleware.maxAttempts'); $i++) {
            $response = $this->actingAs($this->user)->json('get', '/api/v1/search_main', $reapplyData);
            $response->assertStatus(422);
        }
        $response = $this->actingAs($this->user)->json('get', '/api/v1/search_main', $reapplyData);
        $response->assertStatus(429);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/search_reapply', $reapplyData);
        $response->assertStatus(429);
    }

    public function test_update_main()
    {
        $customerOld = Customer::factory()->create();
        $bankOld = Bank::factory()->create();
        $customerToBankOld = CustomerToBank::factory()
            ->for($bankOld)
            ->for($customerOld)
            ->create();

        $reapplyOld = Reapply::factory()
            ->for($customerOld)
            ->create();

        $customerNew = Customer::factory()->test()->make();
        $bankNew = Bank::factory()->make();
        $customerToBankNew = CustomerToBank::factory()
            ->for($bankNew)
            ->for($customerNew)
            ->make();

        $reapplyNew = Reapply::factory()
            ->for($customerNew)
            ->test()
            ->make();

        $newData = array_merge($reapplyNew->toArray(), $customerNew->toArray(), $bankNew->toArray(), $customerToBankNew->toArray());

        $newData['reapply_id'] = $reapplyOld->id;
        $newData['ssn'] = openssl_decrypt($customerOld->ssn, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
        $newData['dob'] = Carbon::createFromFormat('Y-m-d', $newData['dob'])->format('m-d-Y');
        $newData['next_pay_day'] = Carbon::now()->format('m-d-Y');
        $newData['second_pay_day'] = Carbon::now()->addDay()->format('m-d-Y');
        unset($newData['customer_id']);
        unset($newData['bank_id']);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_main', $newData);
        $response->assertStatus(200);

        $customerInDB = $customerNew->toArray();
        unset($customerInDB['ssn']);
        unset($customerInDB['ssn_short']);
        $this->assertDatabaseHas('customers', $customerInDB);
        $this->assertDatabaseHas('cerebro_audit_test.customers', $customerInDB);
        $customerToBankInDB = $customerToBankNew->toArray();
        unset($customerToBankInDB['customer_id']);
        unset($customerToBankInDB['bank_id']);
        $customerToBankInDB['account_number'] = openssl_decrypt($customerToBankNew->account_number, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
        $this->assertDatabaseHas('customers_to_banks', $customerToBankInDB);
        $this->assertDatabaseHas('cerebro_audit_test.customers_to_banks', $customerToBankInDB);
        $this->assertDatabaseHas('banks', $bankNew->toArray());
        $this->assertDatabaseHas('cerebro_audit_test.banks', $bankNew->toArray());

        $reapplyInDB = $reapplyNew->toArray();
        unset($reapplyInDB['customer_id']);
        unset($reapplyInDB['created_at']);
        unset($reapplyInDB['risk']);
        unset($reapplyInDB['auto_redirect_to_lead']);
        $reapplyInDB['next_pay_day'] = Carbon::now()->format('Y-m-d');
        $reapplyInDB['second_pay_day'] = Carbon::now()->addDay()->format('Y-m-d');
        $reapplyInDB['referral_id'] = $this->user->id;
        $this->assertDatabaseHas('reapplies', $reapplyInDB);
        $this->assertDatabaseHas('cerebro_audit_test.reapplies', $reapplyInDB);
    }

    public function test_update_reapply()
    {
        $customerOld = Customer::factory()->create();
        $bankOld = Bank::factory()->create();
        $customerToBankOld = CustomerToBank::factory()
            ->for($bankOld)
            ->for($customerOld)
            ->create();

        $reapplyOld = Reapply::factory()
            ->for($customerOld)
            ->create();

        $customerNew = Customer::factory()->test()->make();
        $bankNew = Bank::factory()->make();
        $customerToBankNew = CustomerToBank::factory()
            ->for($bankNew)
            ->for($customerNew)
            ->make();

        $reapplyNew = Reapply::factory()
            ->for($customerNew)
            ->test()
            ->make();

        $newData = array_merge($reapplyNew->toArray(), $customerNew->toArray(), $bankNew->toArray(), $customerToBankNew->toArray());

        $newData['ssn'] = openssl_decrypt($customerOld->ssn_short, 'aes-128-cbc', config('hashing.hashing_key'), 0, config('hashing.hashing_iv'));
        $newData['email'] = $customerOld->email;
        $newData['next_pay_day'] = Carbon::now()->format('m-d-Y');
        $newData['second_pay_day'] = Carbon::now()->addDay()->format('m-d-Y');
        unset($newData['customer_id']);
        unset($newData['bank_id']);

        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_reapply', $newData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('reapplies', [
            'requested_amount' => $reapplyNew->requested_amount,
            'pay_frequency' => $reapplyNew->pay_frequency,
            'next_pay_day' => Carbon::now()->format('Y-m-d  00:00:00'),
            'second_pay_day' => Carbon::now()->addDay()->format('Y-m-d  00:00:00'),
        ]);

        $this->assertDatabaseHas('cerebro_audit_test.reapplies', [
            'requested_amount' => $reapplyNew->requested_amount,
            'pay_frequency' => $reapplyNew->pay_frequency,
            'next_pay_day' => Carbon::now()->format('Y-m-d  00:00:00'),
            'second_pay_day' => Carbon::now()->addDay()->format('Y-m-d  00:00:00'),
        ]);

        $this->assertDatabaseHas('customers', [
            'credit_score' => $customerNew->credit_score,
            'job_title' => $customerNew->job_title,
        ]);

        $this->assertDatabaseHas('cerebro_audit_test.customers', [
            'credit_score' => $customerNew->credit_score,
            'job_title' => $customerNew->job_title,
        ]);

        $this->assertDatabaseHas('cerebro_audit_test.customers_to_banks', [
            'net_month_income' => $customerToBankNew->net_month_income
        ]);

        $this->assertDatabaseHas('customers_to_banks', [
            'net_month_income' => $customerToBankNew->net_month_income
        ]);
    }

    public function test_get_started_main_flow()
    {
        $reapplyData = $this->reapplyData;
        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->json('post', '/api/v1/get_started', [
            'requested_amount' => 500,
            'email' => "WRONG_EMAIL" . $reapplyData['email']
        ]);
        $response->assertStatus(422);

        $last_4_digits_ssn = substr($reapplyData['ssn'], - 4);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/get_started', [
            'requested_amount' => 500,
            'email' => "WRONG_EMAIL" . $reapplyData['email'],
            'ssn' => $last_4_digits_ssn
        ]);
        $response->assertStatus(404);

        $response = $this->actingAs($this->user)->json('post', '/api/v1/get_started', [
            'requested_amount' => 500,
            'email' => $reapplyData['email'],
            'ssn' => (int) $last_4_digits_ssn + 1
        ]);
        $response->assertStatus(422);

        $last_4_digits_ssn = substr($reapplyData['ssn'], - 4);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/get_started', [
            'requested_amount' => 500,
            'email' => $reapplyData['email'],
            'ssn' => $last_4_digits_ssn
        ]);
        $response->assertStatus(200);
    }

    public function test_get_started_ssn_check()
    {
        $reapplyData = $this->reapplyData;
        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);

        $last_4_digits_ssn = substr($reapplyData['ssn'], - 4);
        for ($i = 0; $i < config('dnm.ssnMiddleware.maxAttempts'); $i++) {
            $response = $this->actingAs($this->user)->json('post', '/api/v1/get_started', [
                'requested_amount' => 500,
                'email' => $reapplyData['email'],
                'ssn' => (int) $last_4_digits_ssn + 1
            ]);
            $response->assertStatus(422);
        }
        $response = $this->actingAs($this->user)->json('post', '/api/v1/get_started', [
            'requested_amount' => 500,
            'email' => $reapplyData['email'],
            'ssn' => (int) $last_4_digits_ssn
        ]);
        $response->assertStatus(429);
    }

    public function test_save_customer_state()
    {
        $reapplyData = $this->reapplyData;
        $dataCustomerState = [
            'click_id' => $reapplyData['click_id'],
            'email' => $reapplyData['email'],
            'url' => $reapplyData['referring_url'],
            'state' => json_encode($reapplyData),
        ];
        $response = $this->actingAs($this->user)->json('post', '/api/v1/save_customer_state', $dataCustomerState);
        $response->assertStatus(201);

        $this->assertDatabaseHas('customer_states', [
            'click_id' => $reapplyData['click_id'],
            'email' => $reapplyData['email'],
            'referral_url' => $reapplyData['referring_url'],
        ]);

        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->json('post', '/api/v1/save_customer_state', $dataCustomerState);
        $response->assertStatus(200);
    }

    public function test_save_customer_to_visitor()
    {
        $reapplyData = $this->reapplyData;
        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('customers_to_visitors', [
            'type' => 'new customer',
            'ip' => $reapplyData['ip_address'],
            'email' => $reapplyData['email'],
            'click_id' => $reapplyData['click_id'],
            'user_agent' => $reapplyData['user_agent'],
            'visitor_id' => 0,
        ]);

        $reapplyData['click_id'] = 'new_clickId';
        $reapplyData['reapply_id'] = $response->baseResponse->original['reapply_id'];
        $reapplyData['customer_id'] = $reapplyData['reapply_id'];

        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_main', $reapplyData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('customers_to_visitors', [
            'type' => 'refill',
            'ip' => $reapplyData['ip_address'],
            'email' => $reapplyData['email'],
            'click_id' => $reapplyData['click_id'],
            'user_agent' => $reapplyData['user_agent'],
            'visitor_id' => 0,
        ]);
        $reapplyData['ssn'] = substr($reapplyData['ssn'], - 4);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_reapply', $reapplyData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('customers_to_visitors', [
            'type' => 'reapply',
            'ip' => $reapplyData['ip_address'],
            'email' => $reapplyData['email'],
            'click_id' => $reapplyData['click_id'],
            'user_agent' => $reapplyData['user_agent'],
            'visitor_id' => 0,
        ]);

        $reapplyData = $this->create_data();
        $visitor = Visitor::factory()->state([
            'click_id' => $reapplyData['click_id'],
            'user_agent' => $reapplyData['user_agent'],
            'referral_id' => $this->user->id,
            'url' => $reapplyData['referring_url'],
            'ip_address' => hash('sha512', $reapplyData['ip_address']),
            'date' => today(),
        ])->create();
        $response = $this->actingAs($this->user)->json('post', '/api/v1/form', $reapplyData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('customers_to_visitors', [
            'type' => 'new customer',
            'ip' => $reapplyData['ip_address'],
            'email' => $reapplyData['email'],
            'click_id' => $reapplyData['click_id'],
            'user_agent' => $reapplyData['user_agent'],
            'visitor_id' => $visitor->id,
        ]);

        $reapplyData['reapply_id'] = $response->baseResponse->original['reapply_id'];
        $reapplyData['customer_id'] = $reapplyData['reapply_id'];

        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_main', $reapplyData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('customers_to_visitors', [
            'type' => 'refill',
            'ip' => $reapplyData['ip_address'],
            'email' => $reapplyData['email'],
            'click_id' => $reapplyData['click_id'],
            'user_agent' => $reapplyData['user_agent'],
            'visitor_id' => $visitor->id,
        ]);
        $reapplyData['ssn'] = substr($reapplyData['ssn'], - 4);
        $response = $this->actingAs($this->user)->json('post', '/api/v1/update_reapply', $reapplyData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('customers_to_visitors', [
            'type' => 'reapply',
            'ip' => $reapplyData['ip_address'],
            'email' => $reapplyData['email'],
            'click_id' => $reapplyData['click_id'],
            'user_agent' => $reapplyData['user_agent'],
            'visitor_id' => $visitor->id,
        ]);
    }

    public function test_visitors_and_clicks()
    {
        $response = $this->actingAs($this->user)->json('get', '/api/v1/count_visitor', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url'],
            'click' => 0,
        ]);
        $clickCounter = 0;
        $visitCounter = 1;
        $response->assertStatus(200);
        $this->assertDatabaseHas('visitors', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url'],
            'clicks_amount' => $clickCounter,
            'visits_amount' => $visitCounter
        ]);

        $response = $this->actingAs($this->user)->json('get', '/api/v1/count_visitor', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url'],
            'click' => 1,
        ]);

        $clickCounter++;
        $visitCounter++;

        $response->assertStatus(200);
        $this->assertDatabaseHas('visitors', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url'],
            'clicks_amount' => $clickCounter,
            'visits_amount' => $visitCounter
        ]);

        $response = $this->actingAs($this->user)->json('get', '/api/v1/count_visitor', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url'],
            'click' => 1,
        ]);

        $clickCounter++;
        $visitCounter++;

        $response->assertStatus(200);
        $this->assertDatabaseHas('visitors', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url'],
            'clicks_amount' => $clickCounter,
            'visits_amount' => $visitCounter
        ]);

        $response = $this->actingAs($this->user)->json('get', '/api/v1/count_click', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url']
        ]);
        $response->assertStatus(200);
        $clickCounter++;
        $this->assertDatabaseHas('visitors', [
            'user_agent' => $this->reapplyData['user_agent'],
            'url' => $this->reapplyData['referring_url'],
            'clicks_amount' => $clickCounter,
            'visits_amount' => $visitCounter
        ]);
    }
}
