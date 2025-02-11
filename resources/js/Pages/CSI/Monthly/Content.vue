<script setup>
    const props = defineProps({
        data: {
            type: Object,
        },
        form: {
            type: Object,
        },
    });

</script>
<template>
    <v-card class="mb-3" v-if="data.cc_data">
    <v-card-title class="bg-gray-500 text-white">
        PART I: CITIZEN'S CHARTER(CC)
    </v-card-title>
    <table style="width:100%; border: 1px solid #333; border-collapse: collapse; padding: 5px" class="text-center">
        <tr>
            <th></th>
            <th></th>
            <th>Number of Respondents who selected</th>
        </tr>
        <tr class="bg-blue-200">
            <th>CC1</th>
            <th colspan="2" class="text-left">Which of the following best describes your awareness of a CC?</th>
        </tr>
        <tr>
            <td>1</td>
            <td class="text-left">I know what a CC is and I saw this office's CC</td>
            <td><span v-if="data.cc_data.cc1_data.cc1_ans1 > 0">{{data.cc_data.cc1_data.cc1_ans1}}</span></td>
        </tr>
        <tr>
            <td>2</td>           
            <td class="text-left">I know what a CC is but I did NOT see this office's CC</td>
            <td><span v-if="data.cc_data.cc1_data.cc1_ans2 > 0">{{data.cc_data.cc1_data.cc1_ans2}}</span></td>
        </tr>
        <tr>
            <td>3</td>
            <td class="text-left">I learned the CC when I saw this office's CC</td>
            <td><span v-if="data.cc_data.cc1_data.cc1_ans3 > 0">{{data.cc_data.cc1_data.cc1_ans3}}</span></td>
        </tr>
        <tr>
            <td>4</td>
            <td class="text-left">I do not know what a CC is and I did not see one in this office. (Answer 'N/A' on CC2 and CC3)</td>
            <td><span v-if="data.cc_data.cc1_data.cc1_ans3 > 0">{{data.cc_data.cc1_data.cc1_ans4}}</span></td>
        </tr>
        <tr class="bg-blue-200" >
            <th >CC2</th>
            <th colspan="2" class="text-left">If aware of CC (answered 1-3 in CC1), would say that the CC of this was...?</th>
        </tr>
        <tr>
            <td>1</td>
            <td class="text-left">Easy to see</td>
            <td><span v-if="data.cc_data.cc1_data.cc1_ans1 > 0">{{data.cc_data.cc2_data.cc2_ans1}}</span></td>
        </tr>
        <tr>
            <td>2</td>
            <td class="text-left">Somewhat easy to see</td>
            <td><span v-if="data.cc_data.cc2_data.cc2_ans2">{{data.cc_data.cc2_data.cc2_ans2}}</span></td>
        </tr>
        <tr>
            <td>3</td>
            <td class="text-left">Difficult to see</td>
            <td><span v-if="data.cc_data.cc2_data.cc2_ans3">{{data.cc_data.cc2_data.cc2_ans3}}</span></td>
        </tr>
        <tr>
            <td>4</td>
            <td class="text-left">Not visible at all</td>
            <td><span v-if="data.cc_data.cc2_data.cc2_ans4">{{data.cc_data.cc2_data.cc2_ans4}}</span></td>
        </tr>
        <tr>
            <td>5</td>
            <td class="text-left">N/A</td>
            <td><span v-if="data.cc_data.cc2_data.cc2_ans5">{{data.cc_data.cc2_data.cc2_ans5}}</span></td>
        </tr>
        <tr class="bg-blue-200">
            <th >CC3</th>
            <th colspan="2" class="text-left">If aware of CC (answered 1-3 in CC1), how much did the CC help you in your transaction?</th>
        </tr>
        <tr>
            <td>1</td>
            <td class="text-left">Helped Very Much</td>
            <td><span v-if="data.cc_data.cc3_data.cc3_ans1">{{data.cc_data.cc3_data.cc3_ans1}}</span></td>
        </tr>
        <tr>
            <td>2</td>
            <td class="text-left">Somewhat helped</td>
            <td><span v-if="data.cc_data.cc3_data.cc3_ans2">{{data.cc_data.cc3_data.cc3_ans2}}</span></td>
        </tr>
        <tr>
            <td>3</td>
            <td class="text-left">Did not help</td>
            <td><span v-if="data.cc_data.cc3_data.cc3_ans3">{{data.cc_data.cc3_data.cc3_ans3}}</span></td>
        </tr>
        <tr>
            <td>4</td>
            <td class="text-left">N/A</td>
            <td><span v-if="data.cc_data.cc3_data.cc3_ans4">{{data.cc_data.cc3_data.cc3_ans4}}</span></td>
        </tr>
    </table>
    </v-card>

    <v-card class="mb-3">
                <v-card-title class="bg-gray-500 text-white">
                    PART II: CUSTOMER RATING OF SERVICE QUALITY
                </v-card-title>
                <table style="width:100%; border: 1px solid #333; border-collapse: collapse;  padding: 3px" >
                   <tr class="text-center">
                        <td rowspan="2">Service Quality Attributes</td>
                        <td >5</td>
                        <td >4</td>
                        <td >3</td>
                        <td >2</td>
                        <td >1</td>
                        <td  rowspan="2">TOTAL SCORE</td>
                        <td  rowspan="2">Likert Scale Rating</td>
                        <td  rowspan="2">GAP</td>
                    </tr>
                    <tr class="text-center">               
                        <td >Very Satisfied</td>
                        <td style="border: 1px solid #333;padding:2px">Satisfied </td>
                        <td >Neither </td>
                        <td  >Dissatisfied </td>
                        <td >Very Dissatisfied </td>
                    </tr>

                    <tr v-for="(dimension, index) in data.dimensions" :key="dimension.id" class="border border-solid hover:bg-gray-100 focus-within:bg-gray-100">                     
                            <td class="border-t p-5 pl-3 ">
                                {{ index + 1 }}. {{ dimension.name }}
                            </td>
                            <td v-if="data.y_totals" class="text-center"  v-for="total in data.y_totals[index+1]">
                                <span v-if="total != 0">{{ total }}</span>
                                <span v-else></span>
                            </td>
                            <td v-if="data.x_totals" class="text-center"  v-for="total in data.x_totals[index+1]">
                                <span v-if="total != 0">{{ total }}</span>
                                <span v-else></span>
                            </td>
                                <td v-if="data.likert_scale_rating_totals" class=" text-center"  v-for="total in data.likert_scale_rating_totals[index+1]">
                                <span v-if="total != 0">{{ total }}</span>
                                <span v-else></span>
                            </td>          
                            <td v-if="data.gap_totals" class="text-center"  v-for="total in data.gap_totals[index+1]">
                                <span v-if="total != 0">{{ total }}</span>
                            </td>                   
                    </tr>
                    <tr class="text-center font-black p-5 m-5 border border-solid hover:bg-gray-100 focus-within:bg-gray-100" >
                        <td class="m-5 p-3">TOTAL SCORE</td>
                        <td ><span v-if="data.grand_vs_total != 0">{{ data.grand_vs_total }} </span> </td>
                        <td ><span v-if="data.grand_s_total != 0">{{ data.grand_s_total }} </span></td>
                        <td ><span v-if="data.grand_n_total != 0">{{ data.grand_n_total }}</span></td>
                        <td ><span v-if="data.grand_d_total != 0">{{ data.grand_d_total }}</span></td>
                        <td ><span v-if="data.grand_vd_total != 0">{{ data.grand_vd_total }}</span></td>
                        <!-- total score -->
                        <td ><span v-if="data.x_grand_total != 0">{{ data.x_grand_total }}</span></td> 
                         <!-- likert scale rating -->
                        <td ><span v-if="data.lsr_grand_total != 0">{{ data.lsr_grand_total }}</span></td>
                        <!--  gap grand total right side or by row-->
                        <td ><span v-if="data.gap_grand_total != 0">{{ data.gap_grand_total }}</span></td>
                    </tr>                                               
                </table>

        
            </v-card> 
            
            <v-card class="mb-3">
                <v-card-title class="bg-gray-500 text-white">
                    PART III: IMPORTANCE OF THIS ATTRIBUTE   
                </v-card-title>
                <v-card-body>
                    <table style="width:100%; border: 1px solid #333; border-collapse: collapse;  padding: 3px">
                       <tr class="text-center">
                            <td  rowspan="2">Importance Service Quality Attributes</td>
                            <td >5</td>
                            <td >4</td>
                            <td >3</td>
                            <td >2</td>
                            <td >1</td>
                            <td  rowspan="2">TOTAL SCORE</td>
                            <td  rowspan="2">Likert Scale Rating</td>
                           <td   rowspan="2">WF</td>
                            <td   rowspan="2">SS</td>
                            <td   rowspan="2">WS</td>
                        </tr>
                        <tr class="text-center">               
                            <td >Very Important</td>
                            <td >Important </td>
                            <td >Moderately Important </td>
                            <td  >Slightly Important  </td>
                            <td >Not All Important </td>
                        </tr>


                        <tr v-for="(dimension, index) in data.dimensions" :key="dimension.id" class="border border-solid hover:bg-gray-100 focus-within:bg-gray-100">
                            
                                <td class="border-t p-3 pl-3 w-1/8 text-left">
                                    {{ index + 1 }}. {{ dimension.name }}
                                </td>
                                <td v-if="data.importance_rate_score_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.importance_rate_score_totals[index+1]">
                                    <span v-if="total != 0">{{ total }}</span>
                                </td>
                                <td v-if="data.x_importance_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.x_importance_totals[index+1]">
                                    <span v-if="total != 0">{{ total }}</span>
                                </td>
                                <td v-if="data.likert_scale_rating_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.importance_ilsr_totals[index+1]">
                                    <span v-if="total != 0">{{ total }}</span>
                                </td>  
                                <td v-if="data.wf_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.wf_totals[index+1]">
                                    <span v-if="total != 0">{{ total }}</span>
                                </td>       
                                <td v-if="data.ss_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.ss_totals[index+1]">
                                    <span v-if="total != 0">{{ total }}</span>
                                </td>  
                                <td v-if="data.ws_totals" class="border-t p-5 w-1/8 text-center mr-10"  v-for="total in data.ws_totals[index+1]">
                                    <span v-if="total != 0">{{ total }}</span>
                                </td>                   
                        </tr>                                           
                    </table>
                </v-card-body>     
            </v-card> 

           
        
            <v-card class="mb-3 bg-none  font-black">
                <v-row class="text-center">
                    <v-col cols="6" >
                        <v-card class="mb-2">
                            <v-card-title class="bg-secondary text-white">
                                    Total No. of Respondents/Customers:   
                            </v-card-title>
                            <v-card-content class="p-5 m-5 text-lg">
                                <span v-if="data.total_respondents"> {{ data.total_respondents }}</span>
                            </v-card-content>
                        </v-card>

                            <v-card class="mb-2">
                            <v-card-title class="bg-secondary text-white">
                                    Total No. of Respondents/Customers who rated VS/S: 
                            </v-card-title>
                            <v-card-content class="p-5 m-5 text-lg">
                                <span v-if="data.total_vss_respondents">{{ data.total_vss_respondents }}</span>
                            </v-card-content>
                        </v-card>
                            <v-card class="mb-2">
                            <v-card-title class="bg-secondary text-white">
                                    Percentage of Respondents/Customers who rated VS/S:       
                            </v-card-title>
                            <v-card-content class="p-5 m-5 text-lg">
                                <span v-if="data.percentage_vss_respondents != 0">{{ data.percentage_vss_respondents }} %</span>
                            </v-card-content>
                        </v-card>

                            <v-card class="mb-2">
                            <v-card-title class="bg-secondary text-white">
                                Customer Satisfaction Score Rating(CSAT) 
                            </v-card-title>
                            <v-card-content class="p-5 m-5 text-lg">
                                <span v-if="data.customer_satisfaction_rating != 0">{{ data.customer_satisfaction_rating }} %</span>
                            </v-card-content>
                        </v-card>
                    </v-col>
                    <v-col cols="6" >
                        <v-card class="mb-2">
                                <v-card-title class="bg-gray-500 text-white">
                                    Customer Satifaction Index(CSI) 
                            </v-card-title>
                            <v-card-content class="p-10 m-5 text-lg text-danger" >
                                <span v-if="data.customer_satisfaction_index > 0">{{ data.customer_satisfaction_index }} %</span>
                            </v-card-content>
                        </v-card>

                        <v-card class="mb-2">
                            <v-card-title class="bg-gray-500 text-white">
                                    Net Promotion Score(NPS)
                            </v-card-title>
                            <v-card-content class="p-5 m-5 text-lg">
                                <span v-if="data.net_promoter_score > 0">{{ data.net_promoter_score }} %</span>
                            </v-card-content>
                        </v-card>
            
                        <v-row>
                            <v-col cols="6">
                                <v-card class="mb-2">
                                        <v-card-title class="bg-gray-500 text-white">
                                        Percentage of Promoters
                                    </v-card-title>
                                    <v-card-content class="p-5 m-5 text-lg">
                                        <span v-if="data.percentage_promoters != 0">{{ data.percentage_promoters }} %</span>
                                    </v-card-content>
                                    
                                </v-card>
                            </v-col>
                                <v-col>
                                <v-card class="mb-2">
                                        <v-card-title class="bg-gray-500 text-white">
                                            Percentage of Detractors
                                        </v-card-title>
                                    <v-card-content class="p-5 m-5 text-lg">
                                        <span v-if="data.percentage_detractors > 0">{{ data.percentage_detractors }} %</span>
                                    </v-card-content>                                        
                                </v-card>
                            </v-col>
                        </v-row>
                            <v-card class="mb-2">
                            <v-card-title class="bg-gray-500 text-white">
                                Likert Scale Rating(Average)
                            </v-card-title>
                            <v-card-content class="p-5 m-5 text-lg">
                                <span v-if="data.lsr_grand_total != 0">{{ data.lsr_grand_total }} %</span>
                            </v-card-content>
                        </v-card>

                        
                    </v-col>
                        
                </v-row>           
            </v-card> 
            <v-card class="mb-3">
                <v-card-title>
                    COMMENTS AND COMPLAINTS: 
                </v-card-title>
                <v-card-content class="m-5 mb-10">
                        <v-row>
                        <div class="ml-10">Comments
                        <v-chip color="primary">
                            {{ data.total_comments }}
                        </v-chip>
                        </div>
                        <div class="">Complaints
                            <v-chip color="red">
                            {{ data.total_complaints }}
                            </v-chip>
                        </div>

                        <template v-for="(comment, index) in data.comments" class="m-5 mb-10">
                            <table style="margin-left: 40px" >
                            <p>[{{ index +1 }}]
                                 {{ comment }}
                                </p>
                            </table>
                        </template>
                    </v-row>
                </v-card-content>
            </v-card>

         

       
            <v-card class="mb-3">
                <v-card-title>
                    ANALYSIS:
                </v-card-title>
                <v-card-content >
                    <div v-if="data.section && data.section.data && data.section.data.length > 0 && data.section.data[0]"  class="m-5" style="margin-top: -10px;text-align: justify;">
                        The  <span>{{ data.section.data[0].section_name }}</span> section had <span>{{ data.total_respondents }}</span> respondents who rated the CSF, 
                        and <span>{{ data.total_vss_respondents }}</span> (or <span>{{ data.percentage_vss_respondents }}</span>%) of those respondents rated 
                        the section with satisfied responses (VS & S) for all service quality attributes. The <span>{{ data.section.data[0].section_name }}</span> section had a 
                        <span>{{ data.customer_satisfaction_index }}</span>% Customer Satisfaction Index as well as a Net Promoter Score of <span>{{ data.net_promoter_score }}</span>. 
                        The Customer Satisfaction Rating for the <span>{{ data.section.data[0].section_name }}</span> 
                        section is <span>{{ data.customer_satisfaction_rating }}</span>%, 
                        which <span v-if="data.customer_satisfaction_rating < 95">does not</span> achieved its functional objective of 95% of customer surveyed are at least satisfied with the services.
                    </div>
                    <div v-else  class="m-5" style="margin-top: -10px;text-align: justify;">
                        The  <span>{{ data.division.division_name }}</span> had <span>{{ data.total_respondents }}</span> respondents who rated the CSF, 
                        and <span>{{ data.total_vss_respondents }}</span> (or <span>{{ data.percentage_vss_respondents }}</span>%) of those respondents rated 
                        the division with satisfied responses (VS & S) for all service quality attributes. The <span>{{ data.division.division_name }}</span> had a 
                        <span>{{ data.customer_satisfaction_index }}</span>% Customer Satisfaction Index as well as a Net Promoter Score of <span>{{ data.net_promoter_score }}</span>. 
                        The Customer Satisfaction Rating for the <span>{{ data.division.division_name }}</span> is <span>{{ data.customer_satisfaction_rating }}</span>%, 
                        which <span v-if="data.customer_satisfaction_rating < 95">does not</span> achieved its functional objective of 95% of customer surveyed are at least satisfied with the services.
                    </div>
                </v-card-content>
            </v-card>

</template>

<style scoped>
   table {
    border-collapse: collapse;
    width: 100%; /* Optional: Set a width for the table */
  }

  tr, th, td {
    border: 1px solid rgb(145, 139, 139); /* Optional: Add a border for better visibility */
    padding: 8px; /* Optional: Add padding for better spacing */
  }
</style>