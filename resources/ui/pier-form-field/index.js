import "./bootstrap";

import PierEditorField from "../form-fields/PierEditorField";
import * as API from "../API";

const defaultConfig = {};

window.addEventListener("PierFormField:loaded", () => {
	console.log("On form loaded....");
})

window.PierFormField = (el, config) => {
	const PierCMSConfig = {
		...defaultConfig,
		...config,
	};

	Vue.component("pier-form-field", PierEditorField);

	new Vue({
		el,
		data() {
			return {
				val: "https://images.unsplash.com/photo-1686676844352-bd0f1fdf3465?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wxNjE2NXwwfDF8YWxsfDl8fHx8fHwyfHwxNjg2NzQ4MTA0fA&ixlib=rb-4.0.3&q=80&w=1080",
			};
		},
		provide() {
			return {
				PierCMSConfig,
				API
			};
		},
	});
};

console.log("Form field loader....");

window.dispatchEvent(new CustomEvent("PierFormField:loaded"));
