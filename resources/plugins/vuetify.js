//import 'vuetify/dist/vuetify.min.css';
import 'vuetify/styles';
import "@mdi/font/css/materialdesignicons.css";

import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";


const Lighttheme = {
    //variables: {},
    colors: {
        background: '#FFFFFF',
        surface: '#FFFFFF',
        primary: '#06402B',
        secondary: '#607D8B',
        indigo: '#3D5AFE',
        dark_indigo: '#1A237E',
        blue_grey: '#263238',
        yellow: '#FDD835',
        light: '#CFD8DC',
        red: '#F44336',
        error: '#B00020',
        info: '#2196F3',
        success: '#4CAF50',
        warning: '#FB8C00',
    },

};

export default createVuetify({
    components,
    directives,
    theme: {
        themes: {
            light: Lighttheme,
        },
    },
    defaults: {
        VBtn: {
            color: "primary",
            rounded: "md",
            flat: true,
            fontWeight: "400",
            letterSpacing: "0",
        },
        VCard: {
            flat: true,
            elevation: 10,
        },

    },
});