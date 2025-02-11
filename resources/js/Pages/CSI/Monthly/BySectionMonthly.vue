<script setup>
    const props = defineProps({
        data: {
            type: Object,
        },
        form: {
            type: Object,
        },
        form_assignatorees: {
            type: Object, 
        }
    });
</script>
<template>

        <div class="print-id print">
            <div style="width: 100%;" v-if="data.cc_data">
               <h5 style="text-transform:capitalize; text-align:center; margin-top: -8px">
                    <div style="display:flex;justify-content:center;align-items:center;">
                        <img
                        data-aos="zoom-in" 
                        data-aos-duration="500" 
                        data-aos-delay="500"
                        style="width:40px; height:40px; margin-right:10px" 
                        src="../../../../../public/images/dar-logo.svg" 
                        alt="..">
                        <span style="text-align:center">CUSTOMER SATISFACTION FEEDBACK <br>SUMMARY REPORT FOR   <u><span>{{ form.selected_month }}</span>  {{ form.selected_year }}</u></span>
                    </div>                  
                </h5><br>
                <div style="display: flex; justify-content: space-between;margin-top: -20px; margin-bottom: 10px" v-if="data.division">
                    <div style="font-size: 12px;">
                        Division : <u>{{ data.division.division_name }}</u> <br>
                       
                    </div>
                    <div style="font-size: 12px" v-if="data.section && data.section.data && data.section.data.length > 0 && data.section.data[0]">  
                       Section : <u v-if="data.section.data.length > 0">{{ data.section.data[0].section_name }}</u><br>
                                <u v-if="form.client_type" style="margin-left: 75px">{{ form.client_type }}</u> <br v-if="form.client_type">
                                <u v-if="data.sub_section[0]">{{ data.sub_section[0].sub_section_name }}</u>
                                <u v-if="form.sub_section_type" style="margin-left: 5px">{{ form.sub_section_type.type_name }}</u>
                    </div>
                    
                </div>

                <div style="margin-top: 20px; margin-bottom: 20px;text-align: center" v-if="data.cc_data">
                    <div style="font-size: 12px;margin-right:20px; margin-bottom:15px; font-weight: bold;text-align: left">
                        PART I: CITIZEN'S CHARTER(CC)
                     </div>
                    <table style="font-size: 13px;width:100%; border: 1px solid #333; border-collapse: collapse;  padding: 3px">
                        <tr>
                            <th></th>
                            <th></th>
                            <th style="border: 1px solid #333;font-size: 12px;">Number of Respondents who selected</th>
                        </tr>
                        <tr class="bg-blue-200">
                            <th>CC1</th>
                            <th colspan="2" style="text-align: left">Which of the following best describes your awareness of a CC?</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td style="text-align: left">I know what a CC is and I saw this office's CC</td>
                            <td><span v-if="data.cc_data.cc1_data.cc1_ans1 > 0">{{data.cc_data.cc1_data.cc1_ans1}}</span></td>
                        </tr>
                        <tr>
                            <td>2</td>           
                            <td style="text-align: left">I know what a CC is but I did NOT see this office's CC</td>
                            <td><span v-if="data.cc_data.cc1_data.cc1_ans2 > 0">{{data.cc_data.cc1_data.cc1_ans2}}</span></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td style="text-align: left">I learned the CC when I saw this office's CC</td>
                            <td><span v-if="data.cc_data.cc1_data.cc1_ans3 > 0">{{data.cc_data.cc1_data.cc1_ans3}}</span></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td style="text-align: left">I do not know what a CC is and I did not see one in this office. (Answer 'N/A' on CC2 and CC3)</td>
                            <td><span v-if="data.cc_data.cc1_data.cc1_ans4 > 0">{{data.cc_data.cc1_data.cc1_ans4}}</span></td>
                        </tr>
                        <tr class="bg-blue-200" >
                            <th >CC2</th>
                            <th colspan="2" style="text-align: left">If aware of CC (answered 1-3 in CC1), would say that the CC of this was...?</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td style="text-align: left">Easy to see</td>
                            <td><span v-if="data.cc_data.cc2_data.cc2_ans1 > 0">{{data.cc_data.cc2_data.cc2_ans1}}</span></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td style="text-align: left">Somewhat easy to see</td>
                            <td><span v-if="data.cc_data.cc2_data.cc2_ans2 > 0">{{data.cc_data.cc2_data.cc2_ans2}}</span></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td style="text-align: left">Difficult to see</td>
                            <td><span v-if="data.cc_data.cc2_data.cc2_ans3 > 0">{{data.cc_data.cc2_data.cc2_ans3}}</span></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td style="text-align: left">Not visible at all</td>
                            <td><span v-if="data.cc_data.cc2_data.cc2_ans4 > 0">{{data.cc_data.cc2_data.cc2_ans4}}</span></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td style="text-align: left">N/A</td>
                            <td><span v-if="data.cc_data.cc2_data.cc2_ans5 > 0">{{data.cc_data.cc2_data.cc2_ans5}}</span></td>
                        </tr>
                        <tr class="bg-blue-200">
                            <th >CC3</th>
                            <th colspan="2" style="text-align: left">If aware of CC (answered 1-3 in CC1), how much did the CC help you in your transaction?</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td style="text-align: left">Helped Very Much</td>
                            <td><span v-if="data.cc_data.cc3_data.cc3_ans1 > 0">{{data.cc_data.cc3_data.cc3_ans1}}</span></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td style="text-align: left">Somewhat helped</td>
                            <td> <span v-if="data.cc_data.cc3_data.cc3_ans2 > 0">{{data.cc_data.cc3_data.cc3_ans2}}</span></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td style="text-align: left">Did not help</td>
                            <td><span v-if="data.cc_data.cc3_data.cc3_ans3 > 0">{{data.cc_data.cc3_data.cc3_ans3}}</span></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td style="text-align: left">N/A</td>
                            <td><span v-if="data.cc_data.cc3_data.cc3_ans4 > 0">{{data.cc_data.cc3_data.cc3_ans4}}</span></td>
                        </tr>
                    </table>
                </div>

                <div style="margin-top: -10px; margin-bottom: 40px; ">
                    <div style="font-size: 12px;margin-right:20px; margin-bottom:15px; font-weight: bold">PART II: CUSTOMER RATING OF SERVICE QUALITY  </div>
                    <table style="font-size: 13px;width:100%; border: 1px solid #333; border-collapse: collapse;  padding: 3px">
                        <tr style="border: 1px solid #333; text-align: center;">
                            <td style="border: 1px solid #333; padding:2px;" rowspan="2">Service Quality Attributes</td>
                            <td style="border: 1px solid #333;">5</td>
                            <td style="border: 1px solid #333;">4</td>
                            <td style="border: 1px solid #333;">3</td>
                            <td style="border: 1px solid #333;">2</td>
                            <td style="border: 1px solid #333;">1</td>
                            <td style="border: 1px solid #333; padding:2px" rowspan="2">TOTAL SCORE</td>
                            <td style="border: 1px solid #333; padding:2px" rowspan="2">Likert Scale Rating</td>
                            <td style="border: 1px solid #333; padding:2px" rowspan="2">GAP</td>
                        </tr>
                        <tr style="border: 1px solid #333; text-align: center;background-color:blue">               
                            <td style="border: 1px solid #333; padding:2px">Very Satisfied</td>
                            <td style="border: 1px solid #333; padding:2px">Satisfied </td>
                            <td style="border: 1px solid #333; padding:2px">Neither </td>
                            <td style="border: 1px solid #333; padding:2px" >Dissatisfied </td>
                            <td style="border: 1px solid #333; padding:2px">Very Dissatisfied </td>
                        </tr>

                        <tr v-for="(dimension, index) in data.dimensions" :key="dimension.id" style="border: 1px solid #333;" >              
                                <td style="border: 1px solid #333; padding:1px; ">
                                    [{{ index + 1 }}] {{ dimension.name }}
                                </td>          
                                <td v-if="data.y_totals" style="border: 1px solid #333; padding: 2px; text-align: center" v-for="total in data.y_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>
                                <td v-if="data.x_totals" style="border: 1px solid #333; padding: 2px; text-align: center"  v-for="total in data.x_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>
                                <td v-if="data.likert_scale_rating_totals" style="border: 1px solid #333; padding: 2px; text-align: center"  v-for="total in data.likert_scale_rating_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>          
                                <td v-if="data.gap_totals" style="border: 1px solid #333; padding: 2px; text-align: center" v-for="total in data.gap_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>                   
                        </tr>

                        <tr style="border: 1px solid #333; text-align: center;">
                            <td style="border: 1px solid #333; ">TOTAL SCORE</td>
                            <td style="border: 1px solid #333;">
                                <span v-if="data.grand_vs_total > 0">{{ data.grand_vs_total }}</span>
                            </td>
                            <td style="border: 1px solid #333;"> 
                                <span v-if="data.grand_s_total > 0">{{ data.grand_s_total }}</span>
                            </td>
                            <td style="border: 1px solid #333;">
                                <span v-if="data.grand_n_total > 0">{{ data.grand_n_total }}</span>
                            </td>
                            <td style="border: 1px solid #333;">
                                <span v-if="data.grand_d_total > 0">{{ data.grand_d_total }}</span>
                            </td>
                            <td style="border: 1px solid #333;">
                                <span v-if="data.grand_vd_total > 0">{{ data.grand_vd_total }}</span>
                            </td>
                            <td style="border: 1px solid #333;">
                                <span v-if="data.x_grand_total > 0">{{ data.x_grand_total }}</span>
                            </td>
                            <td style="border: 1px solid #333;">
                                <span v-if="data.lsr_grand_total > 0">{{ data.lsr_grand_total }}</span>
                            </td>
                            <td style="border: 1px solid #333;">
                                <span v-if="data.gap_grand_total > 0">  {{ data.gap_grand_total }}</span>         
                            </td>
                        </tr>  
                    </table>

                </div>

                <div style="margin-top: 30px">
                    <div style="font-size: 13px;margin-right:20px; margin-bottom:5px; font-weight: bold; page-break-before: always;">PART III: IMPORTANCE OF THIS ATTRIBUTE    </div>
                    <table style="width:100%; border: 1px solid #333; border-collapse: collapse; font-size: 13px;">
                        <tr style="border: 1px solid #333; text-align: center;">
                            <td style="border: 1px solid #333;" rowspan="2">Importance Service Quality Attributes</td>
                            <td style="border: 1px solid #333;">5</td>
                            <td style="border: 1px solid #333;">4</td>
                            <td style="border: 1px solid #333;">3</td>
                            <td style="border: 1px solid #333;">2</td>
                            <td style="border: 1px solid #333;">1</td>
                            <td style="border: 1px solid #333; padding:2px" rowspan="2">TOTAL SCORE</td>
                            <td style="border: 1px solid #333; padding:2px" rowspan="2">Likert Scale Rating</td>
                           <td  style="border: 1px solid #333; padding:2px" rowspan="2">WF</td>
                            <td  style="border: 1px solid #333; padding:2px" rowspan="2">SS</td>
                            <td  style="border: 1px solid #333; padding:2px" rowspan="2">WS</td>
                        </tr>
                        <tr style="border: 1px solid #333; text-align: center;">               
                            <td style="border: 1px solid #333; margin: 5px; padding:2px">Very Important</td>
                            <td style="border: 1px solid #333;  margin: 5px; padding:2px">Important </td>
                            <td style="border: 1px solid #333;  margin: 5px; padding:2px">Moderately Important </td>
                            <td style="border: 1px solid #333;  margin: 5px; padding:px" >Slightly Important  </td>
                            <td style="border: 1px solid #333;  margin: 5px; padding:2px">Not All Important </td>
                        </tr>

                        <tr v-for="(dimension, index) in data.dimensions" :key="dimension.id" style="border: 1px solid #333;" >   
                                 <td style="border: 1px solid #333; padding: 1px;">
                                    [{{ index + 1 }}] {{ dimension.name }}
                                </td>                  
                                <td v-if="data.importance_rate_score_totals" style="border: 1px solid #333; padding: 2px; text-align: center" v-for="total in data.importance_rate_score_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>
                                <td v-if="data.x_importance_totals" style="border: 1px solid #333; padding: 2px; text-align: center" v-for="total in data.x_importance_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>
                                <td v-if="data.likert_scale_rating_totals" style="border: 1px solid #333; padding: 2px; text-align: center" v-for="total in data.importance_ilsr_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>  
                                <td v-if="data.wf_totals" style="border: 1px solid #333; padding: 2px; text-align: center" v-for="total in data.wf_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>       
                                <td v-if="data.ss_totals" style="border: 1px solid #333; padding: 2px; text-align: center" v-for="total in data.ss_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>  
                                <td v-if="data.ws_totals" style="border: 1px solid #333; padding: 2px; text-align: center"  v-for="total in data.ws_totals[index+1]">
                                    <span v-if="total > 0">{{ total }}</span>
                                </td>                                   
                        </tr>


                    </table>

                </div>

                <div style="font-size:13px;margin-top: 20px; text-align:center; text-align: center; font-size: 13px">
                    <table style="width: 100% ;border: 1px solid #333; border-collapse:collapse; margin: auto; ">
                        <tr style="border: 1px solid #333; border-collapse:collapse;">
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 2px">
                                    Total No. of Respondents/Customers:
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 2px" >
                                    <span v-if="data.total_respondents > 0"> {{ data.total_respondents }}</span>
                                </div>
                            </td>

                             <td style="border: 1px solid #333;">
                                <div style="padding: 2px ">
                                    Customer Satisfaction Index (CSI) :
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style=" padding:2px">
                                    <span v-if="data.customer_satisfaction_index > 0">{{ data.customer_satisfaction_index }}</span>
                                </div>
                            </td>
                        </tr>

                        <tr style="border: 1px solid #333; border-collapse:collapse;">
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 2px">
                                    Total No. of Respondents/Customers who rated VS/S:
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 2px">
                                    <span v-if="data.customer_satisfaction_index > 0">{{ data.total_vss_respondents }}</span>
                                </div>
                            </td>

                              <td style="border: 1px solid #333;">
                                <div style=" padding: 2px">
                                   Net Promotion Score: 
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style="padding: 2px">
                                    <span v-if="data.net_promoter_score > 0">{{ data.net_promoter_score }}</span>
                                </div>
                            </td>

                        </tr>

                        <tr style="border: 1px solid #333;">
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 2px">
                                    Percentage of Respondents/Customers who rated VS/S: 
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style="padding: 2px">
                                    <span v-if="data.percentage_vss_respondents > 0 ">{{ data.percentage_vss_respondents }}</span>
                                </div>
                            </td>



                             <td style="border: 1px solid #333;">
                                <div style="padding: 2px">
                                Percentage of Promoters:  
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 2px">
                                    <span v-if="data.percentage_promoters > 0 ">{{ data.percentage_promoters }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr style="border: 1px solid #333;">         
                            <td style="padding: 3px"></td>  
                            <td style="padding: 3px"></td>     
                            <td style="border: 1px solid #333;">
                                <div style="padding: 3px">
                                Percentage of Detractors:  
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 3px">
                                    <span v-if="data.percentage_detractors > 0 ">{{ data.percentage_detractors }}</span>
                                </div>
                            </td>
                        </tr>


                          <tr style="border: 1px solid #333;">
                            <td style="border-collapse:collapse;">
                                <div style=" padding: 3px">
                                Customer Satisfaction  Rating : 
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style="padding: 3px">
                                    <span v-if="data.customer_satisfaction_rating > 0 ">{{ data.customer_satisfaction_rating }}</span>
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style=" padding: 3px">
                                    Likert Scale Rating(Average):
                                </div>
                            </td>
                            <td style="border: 1px solid #333;">
                                <div style="padding: 3px">
                                    <span v-if="data.lsr_grand_total > 0 "> {{ data.lsr_grand_total }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="margin-top: 20px;  font-size: 13px; ">
                    COMMENTS/COMPLAINTS : 
                    <span v-if="data.comments">       
                        <template v-for="(comment, index) in data.comments">
                            <table>
                                <tr style="padding:5px">                          
                                    <span v-if="comment.length > 5">
                                        - {{ comment }} 
                                    </span>
                                </tr>
                            </table>
                        </template>
                    </span>
                    <span v-else>       
                        none
                    </span>
                    <p></p>
                </div>

                 <div style="margin-top: 10px ; font-size: 13px">
                    ANALYSIS : 
                    <div v-if="data.section && data.section.data && data.section.data.length > 0 && data.section.data[0]"  style="text-align: justify; margin: 5px">
                        The  <span>{{ data.section.data[0].section_name }}</span> section had <span>{{ data.total_respondents }}</span> respondents who rated the CSF, 
                        and <span>{{ data.total_vss_respondents }}</span> (or <span>{{ data.percentage_vss_respondents }}</span>%) of those respondents rated 
                        the section with satisfied responses (VS & S) for all service quality attributes. The <span>{{ data.section.data[0].section_name }}</span> section had a 
                        <span>{{ data.customer_satisfaction_index }}</span>% Customer Satisfaction Index as well as a Net Promoter Score of <span>{{ data.net_promoter_score }}</span>. 
                        The Customer Satisfaction Rating for the <span>{{ data.section.data[0].section_name }}</span> 
                        section is <span>{{ data.customer_satisfaction_rating }}</span>%, 
                        which <span v-if="data.customer_satisfaction_rating < 95">does not</span> achieved its functional objective of 95% of customer surveyed are at least satisfied with the services.
                    </div>
                    <div v-else  style="text-align: justify; margin: 5px">
                        The  <span>{{ data.division.division_name }}</span> had <span>{{ data.total_respondents }}</span> respondents who rated the CSF, 
                        and <span>{{ data.total_vss_respondents }}</span> (or <span>{{ data.percentage_vss_respondents }}</span>%) of those respondents rated 
                        the division with satisfied responses (VS & S) for all service quality attributes. The <span>{{ data.division.division_name }}</span> had a 
                        <span>{{ data.customer_satisfaction_index }}</span>% Customer Satisfaction Index as well as a Net Promoter Score of <span>{{ data.net_promoter_score }}</span>. 
                        The Customer Satisfaction Rating for the <span>{{ data.division.division_name }}</span> is <span>{{ data.customer_satisfaction_rating }}</span>%, 
                        which <span v-if="data.customer_satisfaction_rating < 95">does not</span> achieved its functional objective of 95% of customer surveyed are at least satisfied with the services.
                    </div>
                </div>

                <div style="margin-top: 20px; display: flex;  font-size: 13px">
                   <div>
                     Prepared by : <br>
                    <div style="margin-left:50px">
                        <span v-if="form_assignatorees.prepared_by['name'] "><u>{{ form_assignatorees.prepared_by['name'] }}</u></span><br>
                        <span v-if="form_assignatorees.prepared_by['designation'] ">{{ form_assignatorees.prepared_by['designation'] }}</span>
                    </div>
                   </div>
                   <div style="margin: auto; margin-left: 180px;  ">
                     Noted by : 
                    <div style="margin-left:60px">
                        <span v-if="form_assignatorees.noted_by['name'] "><u>{{ form_assignatorees.noted_by['name'] }}</u></span><br>
                        <span v-if="form_assignatorees.noted_by['designation'] ">{{ form_assignatorees.noted_by['designation'] }}</span>
                    </div>

                    <span></span>
                   </div>
                </div>

               

            </div>  
        </div>
</template>
<style scoped>
    .print {
        display: none;
    }
    @media print {
        .print-id {
            display: block;
        }
    
    }

</style>
