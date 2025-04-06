<script setup>
    const props = defineProps({
        data: {
            type: Object,
        },
        form: {
            type: Object,
        },
        is_printing: {
            type: Boolean,
        }
    });
    const calculate = (service_grand_total_score, grand_total_score) => {
        const result = (service_grand_total_score / grand_total_score) * 100;
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
                         <img style="height:120px; height:120px" src="../../../../../../public/images/dar-logo.svg" class="h-8" alt="DAR Logo">                  
                    </div>

                    <div style="text-align: center; margin: auto;margin-bottom: 20px">
                       <span style="font-weight:bold; font-size:25px">Department of Agrarian Reform</span> 
                    </div>

                    <!-- Division and Section/Service Information -->
                    <div style="margin: auto; text-align: center;">
                       <span style="font-weight:bold; font-size:18px">{{ props.data.division ? props.data.division.division_name : '' }}</span><br>
                       <span v-if="props.data.section && props.data.section.data && props.data.section.data[0]" style="font-weight:bold; font-size:18px">
                           {{ props.data.section.data[0].section_name }}
                       </span>
                       <span v-if="props.data.service" style="font-weight:bold; font-size:18px">
                           {{ props.data.service.service_name }}
                       </span>
                    </div>
                    
                    <div style="font-size:13px; margin-bottom:20px">
                        This questionaire aims to solicit your honest assessment of our services. Please take a minute in filling out this form and help us serve you better.
                    </div>
                    
                    <div style="font-size:13px">
                        <label>Email (Optional)</label><br>
                        <input class="v-text-field" type="text" style="width: 100%;">
                    </div>
                    
                    <div style="font-size:13px; margin-bottom:10px">
                        <label>Name (Optional)</label><br>
                        <input class="v-text-field" type="text" style="width: 100%;">
                    </div>
                    
                    <!-- Client Type selection -->
                    <div style="font-size:13px; margin-bottom:15px;">
                        <label style="font-weight:bold;">Client Type*</label><br>
                        <div style="display:flex; flex-wrap:wrap; column-gap: 20px;">
                            <div>
                                <input type="checkbox" id="internal" name="client_type" value="internal">
                                <label for="internal">Internal Employees</label>
                            </div>
                            <div>
                                <input type="checkbox" id="public" name="client_type" value="public">
                                <label for="public">General Public</label>
                            </div>
                            <div>
                                <input type="checkbox" id="government" name="client_type" value="government">
                                <label for="government">Government Employees</label>
                            </div>
                            <div>
                                <input type="checkbox" id="business" name="client_type" value="business">
                                <label for="business">Business/Organization</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Rating Form -->
                    <div style="font-size:13px; margin-bottom:20px;">
                        <h3 style="font-weight:bold; margin-bottom:10px;">Please rate our service</h3>
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr style="background-color:#f2f2f2;">
                                    <th style="padding:8px; text-align:left; border:1px solid #ddd;">Service Aspect</th>
                                    <th style="padding:8px; text-align:center; border:1px solid #ddd;">Poor</th>
                                    <th style="padding:8px; text-align:center; border:1px solid #ddd;">Fair</th>
                                    <th style="padding:8px; text-align:center; border:1px solid #ddd;">Good</th>
                                    <th style="padding:8px; text-align:center; border:1px solid #ddd;">Very Good</th>
                                    <th style="padding:8px; text-align:center; border:1px solid #ddd;">Excellent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:8px; border:1px solid #ddd;">Service Quality</td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="quality" value="1"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="quality" value="2"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="quality" value="3"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="quality" value="4"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="quality" value="5"></td>
                                </tr>
                                <tr>
                                    <td style="padding:8px; border:1px solid #ddd;">Responsiveness</td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="responsiveness" value="1"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="responsiveness" value="2"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="responsiveness" value="3"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="responsiveness" value="4"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="responsiveness" value="5"></td>
                                </tr>
                                <tr>
                                    <td style="padding:8px; border:1px solid #ddd;">Staff Courtesy</td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="courtesy" value="1"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="courtesy" value="2"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="courtesy" value="3"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="courtesy" value="4"></td>
                                    <td style="padding:8px; text-align:center; border:1px solid #ddd;"><input type="radio" name="courtesy" value="5"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Comments Section -->
                    <div style="font-size:13px; margin-bottom:20px;">
                        <label style="font-weight:bold;">Additional Comments</label><br>
                        <textarea class="v-text-field" style="width:100%; height:80px;"></textarea>
                    </div>
                    
                    <!-- QR Code Section -->
                    <div style="text-align:center; margin:20px 0;">
                        <p style="font-size:14px; margin-bottom:10px;">Scan the QR code to fill out this form online</p>
                        <div style="display:inline-block; border:2px solid #ccc; padding:10px;">
                            <!-- QR code will be displayed here -->
                            <div style="width:150px; height:150px; background-color:#f5f5f5; display:flex; align-items:center; justify-content:center;">
                                <span style="font-size:12px; color:#666;">QR Code</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Thank You Message -->
                    <div style="text-align:center; margin:20px 0; padding:10px; background-color:#f9f9f9;">
                        <p style="font-weight:bold;">Thank you for your feedback!</p>
                    </div>
                </nav>
        </div> 
    </div>
</template>
<style scoped>
    .logo {
        height: 10px;
        width: 10px;
    }
</style>