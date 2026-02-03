<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    form: {
        type: Object,
        default: () => ({}),
    },
});

const sumBy = (rows, key) => rows.reduce((total, row) => total + (Number(row[key]) || 0), 0);

const externalTotals = computed(() => {
    const rows = Array.isArray(props.data.external_service_stats) ? props.data.external_service_stats : [];
    return {
        responses: sumBy(rows, 'responses'),
        transactions: sumBy(rows, 'transactions'),
    };
});

const internalTotals = computed(() => {
    const rows = Array.isArray(props.data.internal_service_stats) ? props.data.internal_service_stats : [];
    return {
        responses: sumBy(rows, 'responses'),
        transactions: sumBy(rows, 'transactions'),
    };
});

const totalCustomers = computed(() => Number(props.data.total_customers) || 0);
</script>

<template>
    <div class="print-id print">
        <div style="text-align:center; margin-bottom: 12px;">
            <div style="display:flex;justify-content:center;align-items:center; gap:10px;">
                <img
                    style="width:52px; height:52px" 
                    src="../../../../../../public/images/dar-logo.svg" 
                    alt="DAR">
                <div>
                    <div style="font-size: 16px; font-weight: 700;">DEPARTMENT OF AGRARIAN REFORM</div>
                    <div style="font-size: 14px;">ZAMBOANGA DEL SUR PROVINCIAL OFFICE</div>
                </div>
            </div>
            <div style="margin-top: 12px; font-size: 15px; font-weight: 700;">
                CUSTOMER SATISFACTION FEEDBACK SUMMARY REPORT
            </div>
            <div style="margin-top: 4px; font-size: 13px;">
                <span>For </span>
                <u><span>{{ form.selected_quarter }}</span> {{ form.selected_year }}</u>
            </div>
        </div>

        <div style="margin-top: 20px; font-size: 12px; line-height: 1.5;">
            <div style="font-weight: 700; margin-bottom: 6px;">I. Overview</div>
            <p>
                Client satisfaction surveys were conducted by the Department of Agrarian Reform (DAR) Provincial Office of Zamboanga del Sur
                for the period indicated above. This initiative evaluates the effectiveness and efficiency of the office's services. The evaluation
                focuses on client feedback across eight key Service Quality Dimensions (SQDs): Responsiveness, Assurance, Reliability, Integrity,
                Communication, Access & Facilities, Costs, and Outcome.
            </p>
        </div>

        <div style="margin-top: 18px; font-size: 12px;">
            <div style="font-weight: 700; margin-bottom: 6px;">IV. Data and Interpretation</div>
            <div style="font-weight: 600; margin-bottom: 6px;">A. Services Covered</div>

            <div v-if="Array.isArray(data.external_service_stats) && data.external_service_stats.length" style="margin-top: 10px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">External Services</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 40px; border: 1px solid #333; padding: 3px;">#</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:left;">Services</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Responses</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(service, index) in data.external_service_stats" :key="service.id">
                            <td style="border: 1px solid #333; padding: 3px;">{{ index + 1 }}</td>
                            <td style="border: 1px solid #333; padding: 3px;">{{ service.service_name }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ service.responses }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ service.transactions }}</td>
                        </tr>
                        <tr style="font-weight: 700;">
                            <td colspan="2" style="border: 1px solid #333; padding: 3px; text-align:right;">EXTERNAL SERVICE OVERALL</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ externalTotals.responses }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ externalTotals.transactions }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else-if="Array.isArray(data.external_service_stats)" style="font-size: 12px; margin-top: 8px;">
                No external services found.
            </div>

            <div v-if="Array.isArray(data.internal_service_stats) && data.internal_service_stats.length" style="margin-top: 14px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">Internal Services</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 40px; border: 1px solid #333; padding: 3px;">#</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:left;">Services</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Responses</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(service, index) in data.internal_service_stats" :key="service.id">
                            <td style="border: 1px solid #333; padding: 3px;">{{ index + 1 }}</td>
                            <td style="border: 1px solid #333; padding: 3px;">{{ service.service_name }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ service.responses }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ service.transactions }}</td>
                        </tr>
                        <tr style="font-weight: 700;">
                            <td colspan="2" style="border: 1px solid #333; padding: 3px; text-align:right;">INTERNAL SERVICE OVERALL</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ internalTotals.responses }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ internalTotals.transactions }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else-if="Array.isArray(data.internal_service_stats)" style="font-size: 12px; margin-top: 8px;">
                No internal services found.
            </div>
        </div>

        <div style="margin-top: 22px; font-size: 12px;">
            <div style="font-weight: 600; margin-bottom: 6px;">B. Demographic Profile</div>

            <div style="margin-top: 6px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">Type of Client Served</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <tbody>
                        <tr v-for="row in data.client_types" :key="row.label">
                            <td style="border: 1px solid #333; padding: 3px;">{{ row.label }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.count }}</td>
                        </tr>
                        <tr style="font-weight: 700;">
                            <td style="border: 1px solid #333; padding: 3px;">Total</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ totalCustomers }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 12px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">Age Group</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <tbody>
                        <tr v-for="row in data.age_groups" :key="row.label">
                            <td style="border: 1px solid #333; padding: 3px;">{{ row.label }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.count }}</td>
                        </tr>
                        <tr style="font-weight: 700;">
                            <td style="border: 1px solid #333; padding: 3px;">Total</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ totalCustomers }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 12px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">Sex Disaggregation</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <tbody>
                        <tr v-for="row in data.sexes" :key="row.label">
                            <td style="border: 1px solid #333; padding: 3px;">{{ row.label }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.count }}</td>
                        </tr>
                        <tr style="font-weight: 700;">
                            <td style="border: 1px solid #333; padding: 3px;">Total</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ totalCustomers }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 22px; font-size: 12px;">
            <div style="font-weight: 600; margin-bottom: 6px;">C. Count of CC and SQD Results</div>
            <div v-for="table in data.cc_tables" :key="table.title" style="margin-top: 10px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">{{ table.title }}</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #333; padding: 3px; text-align:left;">Item</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Responses</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in table.rows" :key="row.label">
                            <td style="border: 1px solid #333; padding: 3px;">{{ row.label }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.count }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.percentage }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 22px; font-size: 12px;">
            <div style="font-weight: 600; margin-bottom: 6px;">D. Overall SQD</div>
            <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #333; padding: 3px; text-align:left;">Service Quality Dimensions</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">Strongly Disagree</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">Disagree</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">Neither</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">Agree</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">Strongly Agree</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">N/A</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">Responses</th>
                        <th style="border: 1px solid #333; padding: 3px; text-align:right;">Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in data.sqd_rows" :key="row.label">
                        <td style="border: 1px solid #333; padding: 3px;">{{ row.label }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.strongly_disagree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.disagree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.neither }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.agree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.strongly_agree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.na }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.responses }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ row.rating }}%</td>
                    </tr>
                    <tr style="font-weight: 700;">
                        <td style="border: 1px solid #333; padding: 3px;">TOTAL</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.strongly_disagree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.disagree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.neither }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.agree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.strongly_agree }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.na }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.responses }}</td>
                        <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ data.sqd_totals.rating }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 22px; font-size: 12px;">
            <div style="font-weight: 600; margin-bottom: 6px;">E. Average Score per Service</div>

            <div v-if="Array.isArray(data.external_service_ratings) && data.external_service_ratings.length" style="margin-top: 8px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">External Services</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 40px; border: 1px solid #333; padding: 3px;">#</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:left;">Services</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Overall Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(service, index) in data.external_service_ratings" :key="service.id">
                            <td style="border: 1px solid #333; padding: 3px;">{{ index + 1 }}</td>
                            <td style="border: 1px solid #333; padding: 3px;">{{ service.service_name }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ service.rating === null ? 'N/A' : service.rating + '%' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else-if="Array.isArray(data.external_service_ratings)" style="font-size: 12px; margin-top: 8px;">
                No external service ratings found.
            </div>

            <div v-if="Array.isArray(data.internal_service_ratings) && data.internal_service_ratings.length" style="margin-top: 12px;">
                <div style="font-size: 13px; font-weight: bold; margin-bottom: 5px;">Internal Services</div>
                <table style="font-size: 12px; width:100%; border: 1px solid #333; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 40px; border: 1px solid #333; padding: 3px;">#</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:left;">Services</th>
                            <th style="border: 1px solid #333; padding: 3px; text-align:right;">Overall Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(service, index) in data.internal_service_ratings" :key="service.id">
                            <td style="border: 1px solid #333; padding: 3px;">{{ index + 1 }}</td>
                            <td style="border: 1px solid #333; padding: 3px;">{{ service.service_name }}</td>
                            <td style="border: 1px solid #333; padding: 3px; text-align:right;">{{ service.rating === null ? 'N/A' : service.rating + '%' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else-if="Array.isArray(data.internal_service_ratings)" style="font-size: 12px; margin-top: 8px;">
                No internal service ratings found.
            </div>
        </div>
    </div>
</template>
