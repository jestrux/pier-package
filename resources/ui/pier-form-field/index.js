import "./bootstrap";

import PierEditorField from "../form-fields/PierEditorField";
import * as API from "../API";

Vue.component("pier-form-field", PierEditorField);

document.querySelectorAll(".PierFormFieldWrapper").forEach((node) => {
	const value = node.getAttribute("data-value");
	let field = node.getAttribute("data-field");
	const onChange = eval(node.getAttribute("on-change"));

	try {
		field = JSON.parse(field);
	} catch (error) {}

	new Vue({
		el: node,
		provide() {
			return {
				PierCMSConfig: window.formFieldProps ?? {},
				API,
			};
		},
		render: (h) =>
			h("pier-form-field", {
				props: {
					field,
					value: value ?? "",
				},
				on: {
					input: (e) => (e != value ? onChange.apply(null, [e]) : ""),
				},
			}),
	});
});
