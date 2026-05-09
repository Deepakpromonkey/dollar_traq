<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\CarriersModel\Carrier;
use App\Models\CarriersModel\CarrierBasic;
use App\Models\CarriersModel\CarrierCrash;
use App\Models\CarriersModel\CarrierInspection;
use App\Models\CarriersModel\CarrierFleetSummary;
use App\Models\CarriersModel\CarrierContactHistory;
use App\Models\CarriersModel\CarrierInsurance;
use App\Models\CarriersModel\CarrierCompanySnapshot;
use App\Models\CarriersModel\CarrierContactChange;

class CarrierTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TEST FORMAT METHOD
     */
    public function test_format_method_formats_data_correctly()
    {
      $carrier = new Carrier([
            'legal_name' => 'abc logistics',
        ]);

        $carrier->created_at = '2026-05-08 12:00:00';
        $carrier->updated_at = '2026-05-08 12:00:00';
        $carrier->insurance_limit_amount = 12345.5;

        $formatted = Carrier::format($carrier);

        $this->assertEquals(
            'Abc Logistics',
            $formatted->legal_name
        );

       $this->assertEquals(
            '08-05-2026',
            $formatted->created_at->format('d-m-Y')
        );

        $this->assertEquals(
            '12345.50',
            $formatted->insurance_limit_amount
        );
    }

    /**
     * TEST SEARCH BY DOT NUMBER
     */
    public function test_search_by_dot_number()
    {
        Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '123456',
            'legal_name' => 'Test Carrier'
        ]);

        $request = new Request([
            'dot_number' => '123456'
        ]);

        $result = Carrier::search($request);

        $this->assertEquals(1, $result->total());
    }

    /**
     * TEST SEARCH BY LEGAL NAME
     */
    public function test_search_by_legal_name()
    {
        Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '999999',
            'legal_name' => 'Deepak Transport'
        ]);

        $request = new Request([
            'legal_name' => 'Deepak'
        ]);

        $result = Carrier::search($request);

        $this->assertEquals(1, $result->total());
    }

    /**
     * TEST CACHE WORKING
     */
    public function test_search_uses_cache()
    {
        Cache::shouldReceive('rememberForever')
            ->once()
            ->andReturn(collect([]));

        $request = new Request([
            'dot_number' => '111111'
        ]);

        Carrier::search($request);
    }

    /**
     * TEST FETCH AND STORE
     */
 public function test_fetch_and_store_creates_carrier()
{
    $jsonData = [
        'result' => [
            'data' => [
                'firstItem' => [
                    'dot_number' => '555555',
                    'legal_name' => 'Test Logistics',
                    'usdot_status' => 'ACTIVE',
                    'telephone_number' => '9999999999',
                    'email_address' => 'test@test.com',
                ]
            ]
        ]
    ];

    if (!file_exists(storage_path('app/public'))) {
        mkdir(storage_path('app/public'), 0777, true);
    }

    file_put_contents(
        storage_path('app/public/carriers.json'),
        json_encode($jsonData)
    );

    Carrier::fetchAndStore(new Request());

    $this->assertDatabaseHas('carriers', [
        'dot_number' => '555555',
        'legal_name' => 'Test Logistics'
    ]);
}
    /**
     * TEST RELATIONSHIP - BASICS
     */
    public function test_carrier_has_basics_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '888888',
            'legal_name' => 'Basic Carrier'
        ]);

        CarrierBasic::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
        ]);

        $this->assertCount(
            1,
            $carrier->basics
        );
    }

    /**
     * TEST RELATIONSHIP - CRASHES
     */
    public function test_carrier_has_crashes_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '777777',
            'legal_name' => 'Crash Carrier'
        ]);

        CarrierCrash::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
        ]);

        $this->assertCount(
            1,
            $carrier->crashes
        );
    }

    /**
     * TEST RELATIONSHIP - INSPECTIONS
     */
    public function test_carrier_has_inspections_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '666666',
            'legal_name' => 'Inspection Carrier'
        ]);

        CarrierInspection::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
        ]);

        $this->assertCount(
            1,
            $carrier->inspections
        );
    }

    /**
     * TEST RELATIONSHIP - FLEET SUMMARIES
     */
    public function test_carrier_has_fleet_summaries_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '444444',
            'legal_name' => 'Fleet Carrier'
        ]);

        CarrierFleetSummary::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
        ]);

        $this->assertCount(
            1,
            $carrier->fleetSummaries
        );
    }

    /**
     * TEST RELATIONSHIP - INSURANCES
     */
    public function test_carrier_has_insurances_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '333333',
            'legal_name' => 'Insurance Carrier'
        ]);

        CarrierInsurance::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
        ]);

        $this->assertCount(
            1,
            $carrier->insurances
        );
    }

    /**
     * TEST RELATIONSHIP - CONTACT HISTORIES
     */
    public function test_carrier_has_contact_histories_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '222222',
            'legal_name' => 'History Carrier'
        ]);

        CarrierContactHistory::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
            'physical_address' => 'Delhi'
        ]);

        $this->assertCount(
            1,
            $carrier->contactHistories
        );
    }

    /**
     * TEST PHYSICAL ADDRESS SEARCH
     */
    public function test_search_by_physical_address()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '121212',
            'legal_name' => 'Address Carrier'
        ]);

        CarrierContactHistory::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
            'physical_address' => 'New Delhi India'
        ]);

        $request = new Request([
            'physical_address' => 'Delhi'
        ]);

        $result = Carrier::search($request);

        $this->assertEquals(1, $result->total());
    }

    /**
     * TEST COMPANY SNAPSHOTS RELATION
     */
    public function test_carrier_has_company_snapshots_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '565656',
            'legal_name' => 'Snapshot Carrier'
        ]);

        CarrierCompanySnapshot::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
        ]);

        $this->assertCount(
            1,
            $carrier->companySnapshots
        );
    }

    /**
     * TEST CONTACT CHANGES RELATION
     */
    public function test_carrier_has_contact_changes_relationship()
    {
        $carrier = Carrier::create([
            'row_id' => uniqid(),
            'dot_number' => '787878',
            'legal_name' => 'Contact Change Carrier'
        ]);

        CarrierContactChange::create([
            'row_id' => uniqid(),
            'carrier_id' => $carrier->id,
        ]);

        $this->assertCount(
            1,
            $carrier->contactChanges
        );
    }
}