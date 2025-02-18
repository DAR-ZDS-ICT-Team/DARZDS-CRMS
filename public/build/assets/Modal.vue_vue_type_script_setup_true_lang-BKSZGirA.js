import{s as R}from"./vue-multiselect.css_vue_type_style_index_0_src_true_lang-CRPPjZlv.js";import j from"./BySectionMonthly-Cm64F89u.js";import{P as A}from"./index-DajnbktX.js";import{B as M,d as g,i as D,x as F,j as t,o as y,e as T,b as e,w as o,g as c,t as U,u as $,a as r,c as q,f as z,F as E}from"./app-BcgWRFmc.js";const I=r("span",{class:"text-h5"},"Select Assignatoree",-1),G=r("label",null,"Prepared By:",-1),H=r("label",null,"Noted By:",-1),J={style:{"text-align":"end"}},X=M({__name:"Modal",props:{form:{type:Object,default:null},assignatorees:{type:Object,default:null},user:{type:Object,default:null},value:{type:Boolean,default:!1},data:{type:Boolean},generated:{type:Boolean}},emits:["input"],setup(n,{emit:b}){const p=b,m=n,i=g(!1);D(()=>m.value,l=>{i.value=l});const d=F({prepared_by:m.user,noted_by:{}}),w=l=>{p("input",l)},x=g(!1),h=async()=>{x.value=!0,(await new A).print(document.querySelector(".print-id"),[` 
          @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;800&family=Roboto:wght@100;300;400;500;700;900&display=swap');
          * {
              font-family: 'Time New Roman'
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
            width: 100%; /* Optional: Set a width for the table */
          }

          tr, th, td {
            border: 1px solid rgb(145, 139, 139); /* Optional: Add a border for better visibility */
            padding: 3px; /* Optional: Add padding for better spacing */
          }
          .page-break {
            page-break-before: always; /* or page-break-after: always; */
          }

        `]),p("input",!1)};return(l,a)=>{const k=t("v-card-title"),B=t("v-text-field"),u=t("v-col"),_=t("v-row"),V=t("v-card-text"),C=t("v-spacer"),N=t("v-divider"),f=t("v-icon"),v=t("v-btn"),O=t("v-card-action"),S=t("v-card"),P=t("v-dialog");return y(),T(E,null,[e(P,{modelValue:i.value,"onUpdate:modelValue":a[3]||(a[3]=s=>i.value=s),width:"800",height:"800",scrollable:"",persistent:""},{default:o(()=>[e(S,null,{default:o(()=>[e(k,{class:"bg-indigo"},{default:o(()=>[I]),_:1}),e(V,null,{default:o(()=>[e(_,{style:{"margin-bottom":"-30px"}},{default:o(()=>[e(u,null,{default:o(()=>[G,e(B,{size:"small",variant:"text",readonly:""},{default:o(()=>[c(U(n.user.name),1)]),_:1})]),_:1})]),_:1}),e(_,{style:{"margin-top":"-50px"}},{default:o(()=>[e(u,null,{default:o(()=>[H,e($(R),{modelValue:d.noted_by,"onUpdate:modelValue":a[0]||(a[0]=s=>d.noted_by=s),options:n.assignatorees,multiple:!1,placeholder:"Noted By:",label:"name","track-by":"name","allow-empty":!1,class:"ml-5"},null,8,["modelValue","options"])]),_:1})]),_:1})]),_:1}),e(C),e(O,null,{default:o(()=>[e(N),r("div",J,[e(v,{class:"ma-2",color:"blue-grey-lighten-2",onClick:a[1]||(a[1]=s=>w(!1))},{default:o(()=>[e(f,{start:"",icon:"mdi-cancel"}),c(" Cancel ")]),_:1}),e(v,{class:"ma-2",color:"green-darken-1",type:"button",onClick:a[2]||(a[2]=s=>h())},{default:o(()=>[c(" Print Preview "),e(f,{end:"",icon:"mdi-print"})]),_:1})])]),_:1})]),_:1})]),_:1},8,["modelValue"]),n.form.csi_type=="By Month"?(y(),q(j,{key:0,form:n.form,data:n.data,form_assignatorees:d},null,8,["form","data","form_assignatorees"])):z("",!0)],64)}}});export{X as _};
