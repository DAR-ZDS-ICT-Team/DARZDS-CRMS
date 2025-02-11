<script setup>
    const props = defineProps({
        data: {
            type: Object,
        },
        form: {
            type: Object,
        },
    });
    const calculate = (total_score ,grand_total_score) => {
        const result = (total_score / grand_total_score) * 100;
        return result.toFixed(2);
    };
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
            <th>Number of Respondents who select the option</th>
        </tr>
        <tr class="bg-blue-200">
            <th>CC1</th>
            <th colspan="2" class="text-left">Which of the following best describes your awareness of a CC?</th>
        </tr>
        <tr>
            <td>1</td>
            <td class="text-left">I know what a CC is and I saw this office's CC</td>
            <td>
                <span v-if="data.cc_data.cc1_data.cc1_ans1 > 0">
                    {{ data.cc_data.cc1_data.cc1_ans1 }}
                </span>
            </td>
        </tr>
        <tr>
            <td>2</td>           
            <td class="text-left">I know what a CC is but I did NOT see this office's CC</td>
            <td>
                <span v-if="data.cc_data.cc1_data.cc1_ans2 > 0">
                    {{ data.cc_data.cc1_data.cc1_ans2 }}
                </span>
            </td>
        </tr>
        <tr>
            <td>3</td>
            <td class="text-left">I learned the CC when I saw this office's CC</td>
            <td>
                <span v-if=" data.cc_data.cc1_data.cc1_ans3 > 0">
                    {{ data.cc_data.cc1_data.cc1_ans3 }}
                </span>       
            </td>
        </tr>
        <tr>
            <td>4</td>
            <td class="text-left">I do not know what a CC is and I did not see one in this office. (Answer 'N/A' on CC2 and CC3)</td>
            <td>
                <span v-if="data.cc_data.cc1_data.cc1_ans4 > 0">
                    {{ data.cc_data.cc1_data.cc1_ans4 }}
                </span>   
            </td>
        </tr>
        <tr class="bg-blue-200" >
            <th >CC2</th>
            <th colspan="2" class="text-left">If aware of CC (answered 1-3 in CC1), would say that the CC of this was...?</th>
        </tr>
        <tr>
            <td>1</td>
            <td class="text-left">Easy to see</td>
            <td>
                <span v-if="data.cc_data.cc2_data.cc2_ans1 > 0">
                    {{ data.cc_data.cc2_data.cc2_ans1 }}
                </span>   
            </td>
        </tr>
        <tr>
            <td>2</td>
            <td class="text-left">Somewhat easy to see</td>
            <td>
                <span v-if="data.cc_data.cc2_data.cc2_ans2 > 0">
                    {{ data.cc_data.cc2_data.cc2_ans2 }}
                </span>        
            </td>
        </tr>
        <tr>
            <td>3</td>
            <td class="text-left">Difficult to see</td>
            <td>
                <span v-if="data.cc_data.cc2_data.cc2_ans3 > 0">
                    {{ data.cc_data.cc2_data.cc2_ans3 }}
                </span>   
            </td>
        </tr>
        <tr>
            <td>4</td>
            <td class="text-left">Not visible at all</td>
            <td>
                <span v-if="data.cc_data.cc2_data.cc2_ans4 > 0">
                    {{ data.cc_data.cc2_data.cc2_ans4 }}
                </span>   
            </td>
        </tr>
        <tr>
            <td>5</td>
            <td class="text-left">N/A</td>
            <td>
                <span v-if="data.cc_data.cc2_data.cc2_ans > 0">
                    {{ data.cc_data.cc2_data.cc2_ans }}
                </span>   
            </td>
        </tr>
        <tr class="bg-blue-200">
            <th >CC3</th>
            <th colspan="2" class="text-left">If aware of CC (answered 1-3 in CC1), how much did the CC help you in your transaction?</th>
        </tr>
        <tr>
            <td>1</td>
            <td class="text-left">Helped Very Much</td>
            <td>
                <span v-if="data.cc_data.cc3_data.cc3_ans1 > 0">
                    {{ data.cc_data.cc3_data.cc3_ans1 }}
                </span>   
            </td>
        </tr>
        <tr>
            <td>2</td>
            <td class="text-left">Somewhat helped</td>
            <td>
                <span v-if="data.cc_data.cc3_data.cc3_ans2 > 0">
                    {{ data.cc_data.cc3_data.cc3_ans2 }}
                </span>  
            </td>
        </tr>
        <tr>
            <td>3</td>
            <td class="text-left">Did not help</td>
            <td>
                <span v-if="data.cc_data.cc3_data.cc3_ans3 > 0">
                    {{ data.cc_data.cc3_data.cc3_ans3 }}
                </span>  
            </td>
        </tr>
        <tr>
            <td>4</td>
            <td class="text-left">N/A</td>
            <td>
                <span v-if="data.cc_data.cc3_data.cc3_ans4 > 0">
                    {{ data.cc_data.cc3_data.cc3_ans4 }}
                </span>  

            </td>
        </tr>
    </table>
    </v-card>
    
    <v-card class="mb-3">
        <v-card-title class="bg-gray-500 text-white">
            PART I: CUSTOMER RATING OF SERVICE QUALITY 
        </v-card-title>
        <table class="w-full border ">
            <tr class="text-left font-bold text-center bg-blue-200">
                <th colspan="3">Service Quality Attributes</th>
                <th >OCT</th>
                <th >NOV</th>
                <th >DEC</th>
                <th>Total Raw Points</th>
                <th>Total Score</th>
                <th >Likert Scale Rating</th>
            </tr>

            <template v-for="(dimension, index) in data.dimensions" :key="dimension.id" class="border border-solid hover:bg-gray-100 focus-within:bg-gray-100">                     
                    <tr>
                        <td class="text-center" rowspan="7">
                            [{{ index + 1 }}] {{ dimension.name }}    
                        </td>             
                    </tr> 
                    <tr>
                        <td class="text-left pl-3">5</td>
                        <td>Very Satisfied</td>
                        <td v-if="data.vs_totals" class="text-center"  v-for="total in data.vs_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }} 
                            </span>
                        </td>
                        <td v-if="data.trp_totals" class="text-center" >
                            <span v-if="data.trp_totals[index+1].vs_total_raw_points > 0">
                                {{ data.trp_totals[index+1].vs_total_raw_points }} 
                            </span>
                        </td>
                        <td v-if="data.p1_total_scores" class="text-center" >
                            <span v-if="data.p1_total_scores[index+1].x_vs_total > 0">
                                {{ data.p1_total_scores[index+1].x_vs_total }}
                            </span>
                        </td>
                        <td v-if="data.lsr_totals" class="text-center" >
                            <span v-if="data.lsr_totals[index+1].vs_lsr_total > 0">
                                {{ data.lsr_totals[index+1].vs_lsr_total }} 
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td>Satisfied</td>
                        <td v-if="data.s_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.s_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.trp_totals"  class="text-center" >
                            <span v-if="data.trp_totals[index+1].s_total_raw_points > 0">
                                {{ data.trp_totals[index+1].s_total_raw_points }}
                            </span>
                        </td>
                        <td v-if="data.p1_total_scores" class="text-center" >
                            <span v-if="data.p1_total_scores[index+1].x_s_total > 0">
                                {{ data.p1_total_scores[index+1].x_s_total }}
                            </span>
                        </td>
                        <td v-if="data.lsr_totals" class="text-center" >
                            <span v-if="data.lsr_totals[index+1].s_lsr_total > 0">
                                {{ data.lsr_totals[index+1].s_lsr_total }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>Neither</td>
                        <td v-if="data.n_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.n_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.trp_totals" class="text-center" >
                            <span v-if="data.trp_totals[index+1].n_total_raw_points > 0">
                                {{ data.trp_totals[index+1].n_total_raw_points }}
                            </span>
                        </td>
                        <td v-if="data.p1_total_scores" class="text-center" >
                            <span v-if="data.p1_total_scores[index+1].x_n_total > 0">
                                {{ data.p1_total_scores[index+1].x_n_total }}
                            </span>
                        </td>
                        <td v-if="data.lsr_totals" class="text-center" >
                            <span v-if="data.p1_total_scores[index+1].x_n_total > 0">
                                {{ data.lsr_totals[index+1].n_lsr_total }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                            <td>Dissatisfied</td>
                        <td v-if="data.d_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.d_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.trp_totals" class="text-center" >
                            <span v-if="data.trp_totals[index+1].d_total_raw_points > 0">
                                {{ data.trp_totals[index+1].d_total_raw_points }}
                            </span>
                        </td>
                        <td v-if="data.p1_total_scores" class="text-center" >
                            <span v-if="data.p1_total_scores[index+1].x_d_total > 0">
                                {{ data.p1_total_scores[index+1].x_d_total }}
                            </span>
                        </td>
                        <td v-if="data.lsr_totals" class="text-center" >
                            <span v-if="data.lsr_totals[index+1].d_lsr_total > 0">
                                {{ data.lsr_totals[index+1].d_lsr_total }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                            <td>Very Dissatisfied</td>
                        <td v-if="data.vd_totals" class="border-t p-5 w-1/8 text-center"  v-for="total in data.vd_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }} 
                            </span>
                        </td>
                        <td v-if="data.trp_totals" class="text-center" >
                            <span v-if="data.trp_totals[index+1].vd_total_raw_points > 0">
                                {{ data.trp_totals[index+1].vd_total_raw_points }}
                            </span>
                        </td>
                        <td v-if="data.p1_total_scores" class="text-center" >
                            <span v-if="data.p1_total_scores[index+1].x_vd_total > 0">
                                {{ data.p1_total_scores[index+1].x_vd_total }}
                            </span>
                        </td>
                        <td v-if="data.lsr_totals" class="text-center" >
                            <span v-if="data.lsr_totals[index+1].vd_lsr_total > 0">
                                {{ data.lsr_totals[index+1].vd_lsr_total }} 
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="2"></td>
                        <td v-if="data.grand_totals" class="text-center bg-gray-300"  v-for="total in data.grand_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }} 
                            </span>
                        </td>        
                        <td v-if="data.trp_totals" class="text-center bg-gray-200" >
                            <span v-if="data.trp_totals[index+1].total_raw_points > 0">
                                {{ data.trp_totals[index+1].total_raw_points }}
                            </span>
                        </td>  
                        <td v-if="data.p1_total_scores" class="text-center bg-gray-200" >
                            <span v-if="data.p1_total_scores[index+1].x_total_score > 0">
                                {{ data.p1_total_scores[index+1].x_total_score }}
                            </span>
                        </td>   
                        <td v-if="data.lsr_totals" class="text-center bg-gray-200" >
                            <span v-if="data.lsr_totals[index+1].lsr_total > 0">
                                {{ data.lsr_totals[index+1].lsr_total }}
                            </span>
                        </td>             
                    </tr>

            </template>   

            <!-- totals   -->
            <tr>
                <td></td>
            </tr>
                <tr>
                <td colspan="3" class="text-right">Total No. of Very Satisfied (VS) Responses:</td>
                <td class="text-center">
                    <span v-if="data.first_month_vs_grand_total > 0">
                        {{ data.first_month_vs_grand_total }} 
                    </span>
                </td>
                <td class="text-center"> 
                    <span v-if="data.second_month_vs_grand_total  > 0">
                        {{ data.second_month_vs_grand_total  }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.third_month_vs_grand_total   > 0">
                        {{ data.third_month_vs_grand_total   }}
                    </span>
                 </td>
                <td class="text-center">
                    <span v-if="data.vs_grand_total_raw_points  > 0">
                        {{ data.vs_grand_total_raw_points   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.vs_grand_total_score  > 0">
                        {{ data.vs_grand_total_score   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.vs_grand_total_score  > 0 && data.grand_total_score">
                        {{ calculate(data.vs_grand_total_score, data.grand_total_score) }} 
                    </span>  
                </td>

            </tr>
            <tr>
                <td colspan="3" class="text-right">Total No. of Satisfied (S) Responses:</td>
                <td class="text-center">
                    <span v-if="data.first_mmonth_s_grand_total  > 0">
                        {{ data.first_month_s_grand_total   }}
                    </span>
                </td>
                <td class="text-center"> 
                    <span v-if="data.second_month_s_grand_total  > 0">
                        {{ data.second_month_s_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.third_month_s_grand_total  > 0">
                        {{ data.third_month_s_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.s_grand_total_raw_points  > 0">
                        {{ data.s_grand_total_raw_points   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.s_grand_total_score  > 0">
                        {{ data.s_grand_total_score   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.s_grand_total_score  > 0 && data.grand_total_score  > 0">
                        {{ calculate(data.s_grand_total_score, data.grand_total_score) }}
                    </span>
                </td>


            </tr>
            <tr>
                <td colspan="3" class="text-right">Total No. of Other (N, D, VD) Responses:</td>
                <td class="text-center">
                    <span v-if="data.first_month_ndvd_grand_total  > 0">
                        {{ data.first_month_ndvd_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.second_month_ndvd_grand_total  > 0">
                        {{ data.second_month_ndvd_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.third_month_ndvd_grand_total  > 0">
                        {{ data.third_month_ndvd_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.ndvd_grand_total_raw_points  > 0">
                        {{ data.ndvd_grand_total_raw_points   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.ndvd_grand_total_score  > 0">
                        {{ data.ndvd_grand_total_score   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.ndvd_grand_total_score  > 0 && data.grand_total_score  > 0">
                        {{ calculate(data.ndvd_grand_total_score, data.grand_total_score) }}
                    </span>     
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-right">Total No. of All Responses:</td>
                <td class="text-center">
                    <span v-if="data.first_month_grand_total  > 0">
                        {{ data.first_month_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.second_month_grand_total  > 0">
                        {{ data.second_month_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.third_month_grand_total  > 0">
                        {{ data.third_month_grand_total   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.grand_total_raw_points  > 0">
                        {{ data.grand_total_raw_points   }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.grand_total_score  > 0">
                        {{ data.grand_total_score   }}
                    </span>
                </td>
                <td class="text-center"></td>
            </tr>
            <tr>
                <td colspan="8" class="text-right">Total No. of Respondents/Customers:</td>
                <td class="text-center">
                    <span v-if="data.total_respondents  > 0">
                        {{ data.total_respondents   }}
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="8" class="text-right">Total No. of Respondents/Customers who rated VS or S:</td>
                <td class="text-center">
                    <span v-if="data.total_vss_respondents > 0">
                        {{ data.total_vss_respondents    }}
                    </span>
                </td>
            </tr>
                <tr>
                <td colspan="8" class="text-right">Percentage No. of Respondents/Customers who rated VS or S:</td>
                <td class="text-center">
                    <span v-if="data.percentage_vss_respondents  > 0">
                        {{ data.percentage_vss_respondents   }}
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="8" class="text-right"> Likert Scale Rating (Average):</td>      
                <td class="text-center">
                    <span v-if="data.lsr_average > 0">
                        {{ data.lsr_average   }}
                    </span>
                </td>
            </tr>              
        </table> 
    </v-card> 
            
    <v-card class="mb-3">
        <v-card-title class="bg-gray-500 text-white">
            PART II: IMPORTANCE OF THIS ATTRIBUTE   
        </v-card-title>
            <table class="w-full border ">
            <tr class="text-left font-bold text-center bg-blue-200">
                <th  colspan="3">Importance Service Quality Attributes</th>
                <th >OCT</th>
                <th >NOV</th>
                <th >DEC</th>
                <th >Total Raw Points</th>
                <th  colspan="2">Total Score</th>
            </tr>

            <template v-for="(dimension, index) in data.dimensions" :key="dimension.id" class="border border-solid hover:bg-gray-100 focus-within:bg-gray-100">                     
                    <tr>
                        <td class="text-left pl-3" rowspan="6">
                            [{{ index + 1 }}] {{ dimension.name }}    
                        </td>             
                    </tr> 
                    <tr>
                        <td class="text-center">5</td>
                        <td>Very Important</td>
                        <td v-if="data.vi_totals" class="text-center"  v-for="total in data.vi_totals[index+1]">       
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.i_trp_totals" class="text-center" >
                            <span v-if="data.i_trp_totals[index+1].vi_total_raw_points > 0">
                                {{ data.i_trp_totals[index+1].vi_total_raw_points }}
                            </span>                    
                        </td>
                        <td v-if="data.i_total_scores" class="text-center" >
                            <span v-if="data.i_total_scores[index+1].x_vi_total  > 0">
                                {{ data.i_total_scores[index+1].x_vi_total }}
                            </span>     
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                            <td>Important</td>
                        <td v-if="data.i_totals" class="text-center"  v-for="total in data.i_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.i_trp_totals" class="text-center" >
                            <span v-if="data.i_trp_totals[index+1].i_total_raw_points  > 0">
                                {{ data.i_trp_totals[index+1].i_total_raw_points }}
                            </span>     
                        </td>
                        <td v-if="data.i_total_scores" class="text-center" >
                            <span v-if="data.i_total_scores[index+1].x_i_total  > 0">
                                {{ data.i_total_scores[index+1].x_i_total }}
                            </span>     
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>Moderately Important</td>
                        <td v-if="data.mi_totals" class="text-center"  v-for="total in data.mi_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.i_trp_totals" class="text-center" >
                            <span v-if="data.i_trp_totals[index+1].mi_total_raw_points  > 0">
                                {{ data.i_trp_totals[index+1].mi_total_raw_points }}
                            </span>  
                        </td>
                        <td v-if="data.i_total_scores" class="text-center" >
                            <span v-if="data.i_total_scores[index+1].x_mi_total > 0">
                                {{ data.i_total_scores[index+1].x_mi_total }}
                            </span>  
                        </td>
                        
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                            <td>Slightly Important</td>
                        <td v-if="data.si_totals" class="text-center"  v-for="total in data.si_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.i_trp_totals" class="text-center" >
                            <span v-if="data.i_trp_totals[index+1].si_total_raw_points > 0">
                                {{ data.i_trp_totals[index+1].si_total_raw_points }}
                            </span>  
                        </td>
                        <td v-if="data.i_total_scores" class="text-center" >
                            <span v-if="data.i_total_scores[index+1].x_si_total  > 0">
                                {{  data.i_total_scores[index+1].x_si_total  }}
                            </span>  
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">1</td>
                            <td>Not all Important</td>
                        <td v-if="data.nai_totals" class="text-center"  v-for="total in data.nai_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }}
                            </span>
                        </td>
                        <td v-if="data.i_trp_totals" class="text-center" >
                            <span v-if="data.i_trp_totals[index+1].nai_total_raw_points  > 0">
                                {{  data.i_trp_totals[index+1].nai_total_raw_points  }}
                            </span>  
                        </td>
                        <td v-if="data.i_total_scores" class="text-center" >
                            <span v-if="data.i_total_scores[index+1].x_nai_total  > 0">
                                {{  data.i_total_scores[index+1].x_nai_total  }}
                            </span>  
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td colspan="3"></td>
                            <td v-if="data.grand_totals" class="text-center bg-gray-300"  v-for="total in data.grand_totals[index+1]">
                            <span v-if="total > 0">
                                {{ total }} 
                            </span>
                        </td>        
                        <td v-if="data.i_trp_totals" class="text-center bg-gray-200" >
                            <span v-if="data.i_trp_totals[index+1].total_raw_points  > 0">
                                {{  data.i_trp_totals[index+1].total_raw_points  }} 
                            </span>  
                        </td>  
                        <td v-if="data.i_total_scores" class="text-center bg-gray-200" >
                            <span v-if="data.i_total_scores[index+1].x_i_total_score  > 0">
                                {{  data.i_total_scores[index+1].x_i_total_score  }} 
                            </span>  
                        </td>             
                    </tr>
            </template>                 

            <!-- totals -->
            <tr>
                <td></td>
            </tr>

                <tr class="text-center bg-blue-200">
                <td colspan="3"></td>
                <th >JUL</th>
                <th >AUG</th>
                <th >SEPT</th>
                <td colspan="2">AVERAGE(%)</td>

            </tr>

            <tr>
                <td colspan="3" class="text-right">% of Promoters:</td>
                <td class="text-center" >
                    <span v-if="data.first_month_percentage_promoters  > 0">
                        {{  data.first_month_percentage_promoters  }}
                    </span>  
                </td>  
                <td  class="text-center " >
                    <span v-if="data.second_month_percentage_promoters  > 0">
                        {{  data.second_month_percentage_promoters  }}
                    </span>  
                </td>
                <td  class="text-center" >
                    <span v-if="data.third_month_percentage_promoters  > 0">
                        {{  data.third_month_percentage_promoters  }}
                    </span>  
                </td>
                <td  colspan="2"  class="text-center">
                    <span v-if="data.average_percentage_promoters  > 0">
                        {{  data.average_percentage_promoters  }}
                    </span>  
                </td>

            </tr>
            <tr>
                <td colspan="3" class="text-right">% of Detractors:</td>
                <td  class="text-center" >
                    <span v-if="data.first_month_percentage_detractors  > 0">
                        {{  data.first_month_percentage_detractors  }}
                    </span>  
                </td>  
                <td  class="text-center " >
                    <span v-if="data.second_month_percentage_detractors  > 0">
                        {{  data.second_month_percentage_detractors  }}
                    </span>  
                </td>
                <td  class="text-center" >
                    <span v-if="data.third_month_percentage_detractors  > 0">
                        {{  data.third_month_percentage_detractors  }}
                    </span>  
                </td>
                <td  colspan="2"  class="text-center">
                    <span v-if="data.average_percentage_detractors > 0">
                        {{  data.average_percentage_detractors  }}
                    </span>  
                </td>

            </tr>
            <tr>
                <td colspan="3" class="text-right">Net Promoter Score:</td>
                <td class="text-center" >
                    <span v-if="data.first_month_net_promoter_score > 0">
                        {{  data.first_month_net_promoter_score  }}
                    </span> 
                </td>  
                <td class="text-center " >
                    <span v-if="data.second_month_net_promoter_score > 0">
                        {{  data.second_month_net_promoter_score  }}
                    </span> 
                </td>
                <td class="text-center" >
                    <span v-if="data.third_month_net_promoter_score > 0">
                        {{  data.third_month_net_promoter_score  }}
                    </span> 

                </td>
                <td colspan="2"  class="text-center">
                    <span v-if="data.ave_net_promoter_score > 0">
                        {{  data.ave_net_promoter_score  }}
                    </span> 
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-right">Customer Satisfaction Index (CSI):</td>
                <td class="text-center" >
                    <span v-if="data.first_month_csi > 0" >
                        {{  data.first_month_csi  }}
                    </span> 
                </td>
                <td class="text-center">
                    <span v-if="data.second_month_csi > 0" >
                        {{  data.second_month_csi  }}
                    </span>
                </td>
                <td class="text-center">
                    <span v-if="data.third_month_csi > 0" >
                        {{  data.third_month_csi  }}
                    </span>
                </td>
                <td  colspan="2" class="text-center">
                    <span v-if="data.csi > 0" >
                        {{  data.csi  }}
                    </span>
                </td>
            </tr> 
            <tr>
                <td colspan="8"></td>
                
            </tr>
            <tr>
                <td colspan="3" class="text-right">Customer Satisfaction Rating  :</td>
                <td colspan="5" class="text-center" >
                    <span v-if="data.customer_satisfaction_rating > 0">
                        {{ data.customer_satisfaction_rating }}
                    </span>   
                </td>
            </tr>                                   
        </table>   
    </v-card> 
        
    <v-card class="mb-3">
        <v-card-title>
            COMMENTS AND COMPLAINTS: 
        </v-card-title>
        <v-card-content class="m-5 mb-10">
                <v-row>
                <div class="ml-10">Comments
                <v-chip color="primary">
                    <span v-if="data.total_comments">
                        {{ data.total_comments }}
                    </span>    
                </v-chip>
                </div>
                <div class="">Complaints
                    <v-chip color="red">
                        <span v-if="data.total_complaints">
                            {{ data.total_complaints }}
                        </span>
                    </v-chip>
                </div>

                <template v-if="data.comments" v-for="(comment, index) in data.comments" class="m-5 mb-10">
                    <table style="margin-left: 40px" >
                    <p>[{{ index +1 }}] {{ comment }}</p>
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
                The <span>{{ data.section.section_name }}</span> section for the <span style="text-transform:lowercase">{{ form.selected_quarter }}</span> of <span>{{ form.selected_year }}</span> 
                had a total of <span>{{ data.total_respondents }}</span> respondents who filled out and rated the Customer Satisfaction Feedback. 
                <span>{{ data.total_vss_respondents }}</span> (out of <span>{{ data.total_respondents }}</span>, or <span>{{ data.percentage_vss_respondents }}</span>%) of the respondents rated the CSF as either very satisfied (VS) or satisfied (S), 
                which resulted in an overall average Customer Satisfaction Index (CSI) of <span>{{ data.csi }}</span>%, 
                a Net Promoter Score of {{ data.ave_net_promoter_score }}%, and an average Likert Scale Rating of <span>{{ data.ave_net_promoter_score }}%</span>, which translates to "very satisfied" for 
                the <span style="text-transform:lowercase">{{ form.selected_quarter }}</span> of <span>{{ form.selected_year }}</span>.

                The <span>{{ data.section.section_name }}</span> section's Customer Satisfaction Survey resulted in an Overall Customer Satisfaction Score Rating of <span>{{ data.customer_satisfaction_rating }}</span>% 
                for the <span style="text-transform:lowercase">{{ form.selected_quarter }}</span> of <span>{{ form.selected_year }}</span>, which <span v-if="data.customer_satisfaction_rating < 95">does not</span> achieved its quality objective of at least 95% of customers being satisfied with the services.
            </div>
            <div v-else  class="m-5" style="margin-top: -10px;text-align: justify;">
                The <span>{{ data.division.division_name }}</span> for the <span style="text-transform:lowercase">{{ form.selected_quarter }}</span> of <span>{{ form.selected_year }}</span> 
                had a total of <span>{{ data.total_respondents }}</span> respondents who filled out and rated the Customer Satisfaction Feedback. 
                <span>{{ data.total_vss_respondents }}</span> (out of <span>{{ data.total_respondents }}</span>, or <span>{{ data.percentage_vss_respondents }}</span>%) of the respondents rated the CSF as either very satisfied (VS) or satisfied (S), 
                which resulted in an overall average Customer Satisfaction Index (CSI) of <span>{{ data.csi }}</span>%, 
                a Net Promoter Score of {{ data.ave_net_promoter_score }}%, and an average Likert Scale Rating of <span>{{ data.ave_net_promoter_score }}%</span>, which translates to "very satisfied" for 
                the <span style="text-transform:lowercase">{{ form.selected_quarter }}</span> of <span>{{ form.selected_year }}</span>.

                The <span>{{ data.division.division_name }}</span> Customer Satisfaction Survey resulted in an Overall Customer Satisfaction Score Rating of <span>{{ data.customer_satisfaction_rating }}</span>% 
                for the <span style="text-transform:lowercase">{{ form.selected_quarter }}</span> of <span>{{ form.selected_year }}</span>, which <span v-if="data.customer_satisfaction_rating < 95">does not</span> achieved its quality objective of at least 95% of customers being satisfied with the services. 
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
    padding: 3px; /* Optional: Add padding for better spacing */
  }
</style>