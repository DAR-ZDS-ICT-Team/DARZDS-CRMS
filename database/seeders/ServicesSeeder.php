<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Services;

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
                'slug' => 'program-project-activity-monitoring-and-evaluation',
            ],
            [
                'division_id' => '2',
                'section_id' => null,
                'service_name' => 'ARBO Organizing',
                'slug' => 'arbo-organizing',
            ],
            [
                'division_id' => '2',
                'section_id' => null,
                'service_name' => 'Project Implementation',
                'slug' => 'project-implementation',
            ],
            [
                'division_id' => '2',
                'section_id' => null,
                'service_name' => 'Training, Coaching, Mentoring, and Strengthening of Agrarian Reform Beneficiaries Organizations',
                'slug' => 'training-coaching-mentoring-and-strengthening-of-agrarian-reform-beneficiaries-organizations',
            ],
            [
                'division_id' => '1',
                'section_id' => '1',
                'service_name' => 'Disbursement Vouchers',
                'slug' => 'disbursement-vouchers',
            ],
            [
                'division_id' => '1',
                'section_id' => '2',
                'service_name' => 'Cash Disbursement',
                'slug' => 'cash-disbursement',
            ],
            [
                'division_id' => '1',
                'section_id' => '2',
                'service_name' => 'Collection and Deposit',
                'slug' => 'collection-and-deposit',
            ],
            [
                'division_id' => '1',
                'section_id' => '4',
                'service_name' => 'ICT Service Desk',
                'slug' => 'ict-service-desk',
            ],
            [
                'division_id' => '1',
                'section_id' => '5',
                'service_name' => 'Issuance of Personnel Records',
                'slug' => 'issuance-of-personnel-records',
            ],
            [
                'division_id' => '1',
                'section_id' => '5',
                'service_name' => 'HR Management Control',
                'slug' => 'hr-management-control',
            ],
            [
                'division_id' => '1',
                'section_id' => '6',
                'service_name' => 'Procurement',
                'slug' => 'procurement',
            ],
            [
                'division_id' => '1',
                'section_id' => '6',
                'service_name' => 'Property and Supply Management',
                'slug' => 'property-and-supply-management',
            ],
            [
                'division_id' => '4',
                'section_id' => null,
                'service_name' => 'Land Transfer Clearance',
                'slug' => 'land-transfer-clearance',
            ],
            [
                'division_id' => '4',
                'section_id' => null,
                'service_name' => 'Issuance of Certified True Copies',
                'slug' => 'issuance-of-certified-true-copies',
            ],
            [
                'division_id' => '5',
                'section_id' => null,
                'service_name' => 'DARMO Land Transfer Clearance',
                'slug' => 'darmo-land-transfer-clearance',
            ],
        ];

        Services::insert($services);
    }
}
