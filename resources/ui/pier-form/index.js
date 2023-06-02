import Vue from "vue";

import VueToast from "vue-toast-notification";
import "vue-toast-notification/dist/theme-default.css";

import Form from "./Form";
import Loader from "./Form/components/Loader";

Vue.use(VueToast);
Vue.component("Loader", Loader);

const defaultConfig = {};

window.PierForm = (el = "#pierForm", config) => {
	const PierCMSConfig = {
		...defaultConfig,
		...config,
	};

	new Vue({
		el,
		provide() {
			return {
				model: window.pierModel,
				values: window.pierModelValues,
				PierCMSConfig,
			};
		},
		render: (h) => h(Form),
	}).$mount();
};

window.dispatchEvent(new CustomEvent("PierForm:loaded"));
