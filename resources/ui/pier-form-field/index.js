import "./bootstrap";

import PierEditorField from "../form-fields/PierEditorField";
import * as API from "../API";

Vue.component("pier-form-field", PierEditorField);

document.querySelectorAll(".PierFormFieldWrapper").forEach((node) => {
	const value = node.getAttribute("data-value");
	const model = node.getAttribute("data-model");
	const rowId = node.getAttribute("data-row-id");
	const onChange = eval(node.getAttribute("on-change"));

	let field = node.getAttribute("data-field");

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
					input: async (newValue) => {
						if (newValue == value) return;

						if (model && rowId) {
							await API.updateRecord(model, {
								[field.label || field.name]: newValue,
								_id: rowId,
							});
						}

						newValue != value
							? onChange.apply(null, [newValue])
							: "";
					},
				},
			}),
	});
});
