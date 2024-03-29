import './bootstrap';

import VueToast from "vue-toast-notification";
import "vue-toast-notification/dist/theme-default.css";

import Form from "./Form";
import Loader from "../components/Loader";

Vue.use(VueToast);
Vue.component("Loader", Loader);

const defaultConfig = {};

window.PierForm = (el = "#pierForm", config) => {
	const PierCMSConfig = {
		...defaultConfig,
		...config,
	};

	window.__pierForm = config.pierForm;

	new Vue({
		el,
		provide() {
			return {
				model: config.pierModel,
				values: config.pierModelValues,
				PierCMSConfig,
			};
		},
		render: (h) => h(Form),
	}).$mount();
};

window.dispatchEvent(new CustomEvent("PierForm:loaded"));
