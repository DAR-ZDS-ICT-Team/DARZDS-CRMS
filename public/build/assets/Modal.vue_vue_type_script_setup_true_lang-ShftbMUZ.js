import{s as O}from"./vue-multiselect.css_vue_type_style_index_0_src_true_lang-BlSXXW6C.js";import R from"./BySectionMonthly-CKobLhlD.js";import{P as E}from"./index-DajnbktX.js";import{A as M,d as b,z as T,p as z,s as t,o as w,e as A,b as e,w as o,g as p,t as D,u as F,a as s,c as U,f as $,F as q}from"./app-DRGb4qkM.js";const I=s("span",{class:"text-h5"},"Select Assignatoree",-1),G=s("label",null,"Prepared By:",-1),H=s("label",null,"Noted By:",-1),J={style:{"text-align":"end"}},K={style:{display:"none"}},Y=M({__name:"Modal",props:{form:{type:Object,default:null},assignatorees:{type:Object,default:null},user:{type:Object,default:null},value:{type:Boolean,default:!1},data:{type:Object},generated:{type:Object}},emits:["input"],setup(l,{emit:x}){const h=x,m=l,i=b(!1),c=b(!1);T(()=>m.value,a=>{i.value=a});const d=z({prepared_by:m.user,noted_by:{}}),u=a=>{h("input",a)},k=async()=>{c.value=!0,setTimeout(async()=>{try{console.log("Starting print process");let a=document.querySelector(".print-id");if(console.log("Print element found:",!!a),!a){console.error("Print target not found"),alert("Error: Could not find content to print. Make sure the report is generated."),c.value=!1;return}await new E().print(a,[` 
                @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;800&family=Roboto:wght@100;300;400;500;700;900&display=swap');
                * {
                    font-family: 'Times New Roman'
                }
                .new-page {
                    page-break-before: always;
                }
                .th-color{
                    background-color: #8fd1e8;
                }
                .text-center{
                    text-align: center;
                }
                .text-right{
                    text-align:end
                }
                table {
                    border-collapse: collapse;
                    width: 100%;
                }
                tr, th, td {
                    border: 1px solid rgb(145, 139, 139);
                    padding: 3px;
                }
                .page-break {
                    page-break-before: always;
                }
                @media print {
                    body {
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }
            `]),u(!1)}catch(a){console.error("Print error:",a),alert("Error printing: "+a.message)}finally{c.value=!1}},500)};return(a,n)=>{const _=t("v-card-title"),B=t("v-text-field"),f=t("v-col"),g=t("v-row"),V=t("v-card-text"),C=t("v-spacer"),P=t("v-divider"),v=t("v-icon"),y=t("v-btn"),j=t("v-card-action"),N=t("v-card"),S=t("v-dialog");return w(),A(q,null,[e(S,{modelValue:i.value,"onUpdate:modelValue":n[3]||(n[3]=r=>i.value=r),width:"800",height:"800",scrollable:"",persistent:""},{default:o(()=>[e(N,null,{default:o(()=>[e(_,{class:"bg-indigo"},{default:o(()=>[I]),_:1}),e(V,null,{default:o(()=>[e(g,{style:{"margin-bottom":"-30px"}},{default:o(()=>[e(f,null,{default:o(()=>[G,e(B,{size:"small",variant:"text",readonly:""},{default:o(()=>[p(D(l.user.name),1)]),_:1})]),_:1})]),_:1}),e(g,{style:{"margin-top":"-50px"}},{default:o(()=>[e(f,null,{default:o(()=>[H,e(F(O),{modelValue:d.noted_by,"onUpdate:modelValue":n[0]||(n[0]=r=>d.noted_by=r),options:l.assignatorees,multiple:!1,placeholder:"Noted By:",label:"name","track-by":"name","allow-empty":!1,class:"ml-5"},null,8,["modelValue","options"])]),_:1})]),_:1})]),_:1}),e(C),e(j,null,{default:o(()=>[e(P),s("div",J,[e(y,{class:"ma-2",color:"blue-grey-lighten-2",onClick:n[1]||(n[1]=r=>u(!1))},{default:o(()=>[e(v,{start:"",icon:"mdi-cancel"}),p(" Cancel ")]),_:1}),e(y,{class:"ma-2",color:"green-darken-1",type:"button",onClick:n[2]||(n[2]=r=>k())},{default:o(()=>[p(" Print Preview "),e(v,{end:"",icon:"mdi-print"})]),_:1})])]),_:1})]),_:1})]),_:1},8,["modelValue"]),s("div",K,[l.form.csi_type=="By Month"?(w(),U(R,{key:0,form:l.form,data:l.data,form_assignatorees:d,class:"print-id"},null,8,["form","data","form_assignatorees"])):$("",!0)])],64)}}});export{Y as _};
