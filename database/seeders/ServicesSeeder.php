<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Services;
use Illuminate\Support\Facades\DB;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // [
            //     'division_id' => '1',
            //     'section_id' => null,
            //     'service_name' => 'Handling of Customer Complaints',
            //     'slug' => 'handling-of-customer-complaints',
            // ],
            [
                'division_id' => '2',
                'section_id' => null,
                'service_name' => 'Program/Project/Activity Monitoring and Evaluation',
                'service_type' => 'External',
                'slug' => 'program-project-activity-monitoring-and-evaluation',
            ],
            [
                'division_id' => '2',
                'section_id' => null,
                'service_name' => 'ARBO Organizing',
                'service_type' => 'External',
                'slug' => 'arbo-organizing',
            ],
            [
                'division_id' => '2',
                'section_id' => null,
                'service_name' => 'Project Implementation',
                'service_type' => 'External',
                'slug' => 'project-implementation',
            ],
            [
                'division_id' => '2',
                'section_id' => null,
                'service_name' => 'Training, Coaching, Mentoring, and Strengthening of Agrarian Reform Beneficiaries Organizations',
                'service_type' => 'External',
                'slug' => 'training-coaching-mentoring-and-strengthening-of-agrarian-reform-beneficiaries-organizations',
            ],
            [
                'division_id' => '1',
                'section_id' => '1',
                'service_name' => 'Disbursement Vouchers',
                'service_type' => 'Internal',
                'slug' => 'disbursement-vouchers',
            ],
            [
                'division_id' => '1',
                'section_id' => '2',
                'service_name' => 'Cash Disbursement',
                'service_type' => 'Internal',
                'slug' => 'cash-disbursement',
            ],
            [
                'division_id' => '1',
                'section_id' => '2',
                'service_name' => 'Collection and Deposit',
                'service_type' => 'Internal',
                'slug' => 'collection-and-deposit',
            ],
            [
                'division_id' => '1',
                'section_id' => '4',
                'service_name' => 'ICT Service Desk',
                'service_type' => 'Internal',
                'slug' => 'ict-service-desk',
            ],
            [
                'division_id' => '1',
                'section_id' => '5',
                'service_name' => 'Issuance of Personnel Records',
                'service_type' => 'Internal',
                'slug' => 'issuance-of-personnel-records',
            ],
            [
                'division_id' => '1',
                'section_id' => '5',
                'service_name' => 'HR Management Control',
                'service_type' => 'Internal',
                'slug' => 'hr-management-control',
            ],
            [
                'division_id' => '1',
                'section_id' => '6',
                'service_name' => 'Procurement',
                'service_type' => 'Internal',
                'slug' => 'procurement',
            ],
            [
                'division_id' => '1',
                'section_id' => '6',
                'service_name' => 'Property and Supply Management',
                'service_type' => 'Internal',
                'slug' => 'property-and-supply-management',
            ],
            [
                'division_id' => '4',
                'section_id' => null,
                'service_name' => 'Land Transfer Clearance',
                'service_type' => 'External',
                'slug' => 'land-transfer-clearance',
            ],
            [
                'division_id' => '4',
                'section_id' => null,
                'service_name' => 'Issuance of Certified True Copies',
                'service_type' => 'External',
                'slug' => 'issuance-of-certified-true-copies',
            ],
            [
                'division_id' => '5',
                'section_id' => null,
                'service_name' => 'DARMO Land Transfer Clearance',
                'service_type' => 'External',
                'slug' => 'darmo-land-transfer-clearance',
            ],
        ];

        DB::transaction(function () use ($services) {
            foreach ($services as $service) {
                Services::updateOrCreate(
                    ['slug' => $service['slug']],
                    $service
                );
            }
        });
    }
}
