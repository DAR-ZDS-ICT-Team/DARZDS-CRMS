<script setup>
import { key } from 'fontawesome';

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
                <th>Number of Respondents who select the option</th>
            </tr>
            <tr class="bg-blue-200">
                <th>CC1</th>
                <th colspan="2" class="text-left">Which of the following best describes your awareness of a CC?</th>
            </tr>
            <tr>
                <td>1</td>
                <td class="text-left">I know what a CC is and I saw this office's CC</td>
                <td>{{data.cc_data.cc1_data.cc1_ans1}}</td>
            </tr>
            <tr>
                <td>2</td>           
                <td class="text-left">I know what a CC is but I did NOT see this office's CC</td>
                <td>{{data.cc_data.cc1_data.cc1_ans2}}</td>
            </tr>
            <tr>
                <td>3</td>
                <td class="text-left">I learned the CC when I saw this office's CC</td>
                <td>{{data.cc_data.cc1_data.cc1_ans3}}</td>
            </tr>
            <tr>
                <td>4</td>
                <td class="text-left">I do not know what a CC is and I did not see one in this office. (Answer 'N/A' on CC2 and CC3)</td>
                <td>{{data.cc_data.cc1_data.cc1_ans4}}</td>
            </tr>
            <tr class="bg-blue-200" >
                <th >CC2</th>
                <th colspan="2" class="text-left">If aware of CC (answered 1-3 in CC1), would say that the CC of this was...?</th>
            </tr>
            <tr>
                <td>1</td>
                <td class="text-left">Easy to see</td>
                <td>{{data.cc_data.cc2_data.cc2_ans1}}</td>
            </tr>
            <tr>
                <td>2</td>
                <td class="text-left">Somewhat easy to see</td>
                <td>{{data.cc_data.cc2_data.cc2_ans2}}</td>
            </tr>
            <tr>
                <td>3</td>
                <td class="text-left">Difficult to see</td>
                <td>{{data.cc_data.cc2_data.cc2_ans3}}</td>
            </tr>
            <tr>
                <td>4</td>
                <td class="text-left">Not visible at all</td>
                <td>{{data.cc_data.cc2_data.cc2_ans4}}</td>
            </tr>
            <tr>
                <td>5</td>
                <td class="text-left">N/A</td>
                <td>{{data.cc_data.cc2_data.cc2_ans5}}</td>
            </tr>
            <tr class="bg-blue-200">
                <th >CC3</th>
                <th colspan="2" class="text-left">If aware of CC (answered 1-3 in CC1), how much did the CC help you in your transaction?</th>
            </tr>
            <tr>
                <td>1</td>
                <td class="text-left">Helped Very Much</td>
                <td>{{data.cc_data.cc3_data.cc3_ans1}}</td>
            </tr>
            <tr>
                <td>2</td>
                <td class="text-left">Somewhat helped</td>
                <td>{{data.cc_data.cc3_data.cc3_ans2}}</td>
            </tr>
            <tr>
                <td>3</td>
                <td class="text-left">Did not help</td>
                <td>{{data.cc_data.cc3_data.cc3_ans3}}</td>
            </tr>
            <tr>
                <td>4</td>
                <td class="text-left">N/A</td>
                <td>{{data.cc_data.cc3_data.cc3_ans4}}</td>
            </tr>
        </table>
    </v-card>

    <v-card>

  <div class="m-5">
    <table>
      <thead>
        <tr>
          <th>Service Unit</th>
          <th>Total No. of Respondents</th>
          <th>Total No. of Respondents who rated VS and S</th>
          <th>Percentage of Respondents who rated VS and S</th>
          <th>Customer Satisfaction Rating</th>
          <th>Customer Satisfaction Index (CSI)</th>
          <th>Net Promoter Score</th>
          <th>Likert Scale Rating (Attribute Average)</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="(service, index) in data.services_units.data" :key="index">
            <tr class="bg-blue-200">
                <td colspan="8">{{  service.services_name  }}</td>
            </tr>
            <template v-for="(unit,index) in service.units" :key="index">
                <tr>
                    <td class="pl-5"> {{ unit.unit_name }}</td>

                    <!-- Total Respondents -->
                    <td class="text-center" v-if="service.id == 1 && unit.id == 1">{{ data.ord_total_respondents.secretariat_total_respo  }}</td>
                    <td class="text-center" v-if="service.id == 1 && unit.id == 2">{{ data.ord_total_respondents.gad_total_respo  }}</td>
                    <td class="text-center" v-if="service.id == 1 && unit.id == 3">{{ data.ord_total_respondents.fairness_total_respo  }}</td>
                     <!-- Total Respondents who rated VS or S -->
                    <td class="text-center" v-if="service.id == 1 && unit.id == 1">{{ data.ord_total_vss_respondents.secretariat_total_vss_respo  }}</td>
                    <td class="text-center" v-if="service.id == 1 && unit.id == 2">{{ data.ord_total_vss_respondents.gad_total_vss_respo  }}</td>
                    <td class="text-center" v-if="service.id == 1 && unit.id == 3">{{ data.ord_total_vss_respondents.fairness_total_vss_respo  }}</td>
                    <!-- Percentage Respondents who rated VS or S -->
                    <td class="text-center" v-if="service.id == 1 && unit.id == 1">{{ data.ord_percentage_vss_respondents.secretariat_percentage_vss_respo  }}%</td>
                    <td class="text-center" v-if="service.id == 1 && unit.id == 2">{{ data.ord_percentage_vss_respondents.gad_percentage_vss_respo  }}%</td>
                    <td class="text-center" v-if="service.id == 1 && unit.id == 3">{{ data.ord_percentage_vss_respondents.fairness_percentage_vss_respo  }}%</td>
                    <!-- csi_total -->
                     <td class="text-center">{{ csi_total }}</td>
                </tr>
                <template  v-for="sub_unit in unit.sub_units">
                    <tr>
                        <td  class="pl-10">
                            <li style="list-style-type:square">
                                {{ sub_unit.sub_unit_name }}
                            </li> 
                        </td>
                    </tr>
                    <template  v-for="type in sub_unit.sub_unit_types">
                        <tr >
                            <td class="pl-16">
                                <ul>
                                    <li style="list-style-type: circle;">
                                        {{ type.type_name }}
                                    </li>
                                   
                                </ul>
                            </td>                      
                        </tr>
                    </template>

                    <template  v-for="psto in sub_unit.sub_unit_pstos">
                        <tr v-if="psto.id == 48 || psto.id == 49  ||
                                  psto.id == 50 || psto.id == 51">
                            <td class="pl-16">
                                <ul>
                                    <li style="list-style-type: circle;" >
                                        {{ psto.psto_name }}
                                    </li>
                                   
                                </ul>
                            </td>                      
                        </tr>
                    </template>
                </template>


                <template  v-for="psto in unit.unit_pstos">
                        <tr v-if="psto.id == 48 || psto.id == 49  ||
                                  psto.id == 50 || psto.id == 51">
                            <td class="pl-10">
                                <ul>
                                    <li style="list-style-type: circle;">
                                        {{ psto.psto_name }}
                                    </li>
                                   
                                </ul>
                            </td>                      
                        </tr>
                    </template>
            </template>
         
        </template>
      
        <tr class="total-row">
          <td>Total:</td>
          <td>260</td>
          <td>260</td>
          <td>100.00</td>
          <td>100.00</td>
          <td>99.44</td>
          <td>95.77</td>
          <td>4.96</td>
        </tr>

        <!-- Add more sections here similarly -->
        <!-- ... -->

     
      </tbody>
    </table>
    <div class="assessment m-5">
      <p>
        The Department of Science and Technology IX for the year 2023 had a total of 3,786 respondents who filled out and rated the Customer Satisfaction Feedback. 3,760 (out of 3,786, or 99.31%) of the respondents rated the CSF as either very satisfied (VS) or satisfied (S), which resulted in an overall average Customer Satisfaction Index (CSI) of 98.09% and a Net Promoter Score of 90.0%.
      </p>
      <p>
        For the year 2023, through the DOST-IX's Customer Relations Management System, the survey instruments were all aligned with the 8 Service Quality Dimensions (SQD) and scored using a 5-point Likert scale rating. The interpretation of the results is as follows: 1.00-1.49 = very unsatisfied; 1.50-2.49 = unsatisfied; 2.50-3.49 = neither unsatisfied nor satisfied; 3.50-4.49 = satisfied; and 4.50-5.00 = very satisfied. As a result, DOST-IX recorded an overall score of 4.90, which translates to "very satisfied" for the year 2023.
      </p>
      <p>
        The Department of Science and Technology IX Customer Satisfaction Survey resulted in an Overall Customer Satisfaction Rating of 99.73% for the year 2023, which achieved its quality objective of at least 95% and remained consistent with the satisfaction rating of the previous year (2022), which was 99.53%. This consistent high level of customer satisfaction demonstrates DOST-IX's commitment to delivering an exceptional customer experience.
      </p>
    </div>
  </div>
</v-card>

</template>

<style scoped>
body {
  font-family: Arial, sans-serif;
  margin: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}
tr,th, td {
  border: 1px solid #dddddd;
  padding: 2px;
}
.total-row {
  font-weight: bold;
  background-color: #f9f9f9;
}
.section-title {
  font-weight: bold;
  text-align: left;
}
.assessment {
  margin-top: 20px;
}
</style>