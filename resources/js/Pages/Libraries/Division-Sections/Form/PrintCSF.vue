<script setup>
    const props = defineProps({
        data: {
            type: Object,
        },
        form: {
            type: Object,
        },
        is_printing: {
            type:Boolean,
        }
    });
    const calculate = (ndvd_grand_total_score ,grand_total_score) => {
        const result = (ndvd_grand_total_score / grand_total_score) * 100;
        return result.toFixed(2);
    };
</script>
<template>
    <div class="print-id print" v-if="is_printing">
        <div style="width: 100%;">
                <nav 
                    data-aos="fade-down" 
                    data-aos-duration="500" 
                    data-aos-delay="500" 
                    class="bg-white dark:bg-gray-900 fixed w-full z-20 top-0 start-0 border-b border-gray-200 dark:border-gray-600">
                    <div style="text-align: center; margin: auto;">
                         <img style="height:120px; height:120px" src="../../../../../../public/images/dar-logo.svg" class="h-8" alt="DOST Logo">                  
                    </div>

                    <div style="text-align: center; margin: auto;margin-bottom: 20px">
                       <span style="font-weight:bold; font-size:25px">Department of Science and Technology</span> 
                    </div>

                     <div style="margin: auto;">
                       <span style="font-weight:bold; font-size:18px">Technology Training and Consultancy</span> <br>
                       <span style="font-weight:bold; font-size:18px">Manufacturing Productivity Extension Program Zamboanga Del Sur</span> 
                    </div>
                    <div style="font-size:13px ; margin-bottom:20px">
                        This questionaire aims to solicit your honest assessment of our services. Please take a minute in filling out this form and help us serve you better.
                    </div>
                     <div style="font-size:13px">
                        <label>Email(Optional)</label><br>
                        <input class="v-text-field" type="text" style="width: 100%;">
                    </div>
                    <div style="font-size:13px;margin-bottom:10px">
                        <label>Name(Optional)</label><br>
                        <input class="v-text-field" type="text" style="width: 100%;">
                    </div>
                    <div style="display:flex;font-size:13px;justify-content: space-evenly;">
                        <div >
                            <label>Client Type*</label><br>
                                <input type="checkbox" >
                                <label>Internal Employees</label><br>
                                <input type="checkbox">
                                <label for="checkbox2">General Public</label><br>
                                <input type="checkbox">
                                <label>Government Employees</label><br>
                                <input type="checkbox">
                                <label>Business/Organization</label><br>
                        </div>
                        <div>
                            <label>Client Type*</label><br>
                                <input type="checkbox" >
                                <label>Internal Employees</label><br>
                                <input type="checkbox">
                                <label for="checkbox2">General Public</label><br>
                                <input type="checkbox">
                                <label>Government Employees</label><br>
                                <input type="checkbox">
                                <label>Business/Organization</label><br>
                        </div>
                         <div>
                            <label>Client Type*</label><br>
                                <input type="checkbox" >
                                <label>Internal Employees</label><br>
                                <input type="checkbox">
                                <label for="checkbox2">General Public</label><br>
                                <input type="checkbox">
                                <label>Government Employees</label><br>
                                <input type="checkbox">
                                <label>Business/Organization</label><br>
                        </div>
                    </div>
                    
                </nav>
                

           
        </div> 
    </div>
</template>
<style scoped>
    .logo{
        height: 10px;
        width: 10px;
    }
    
</style>
