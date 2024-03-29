import { mapState, mapGetters } from "vuex";
import { handleNetworkError } from "../../../Utils";

export default {
	props: {
		values: Object,
	},
	inject: ["API"],
	mounted() {
		window.showPierReferenceModalForm = (modelName) => {
			const model = this.models.find(({ name }) => name == modelName);
			try {
				model.fields = JSON.parse(model.fields);
			} catch (error) {}
			this.referenceModel = model;

			return new Promise((res) => {
				this.referenceModalResolver = (data) => {
					this.referenceModel = null;
					res(data);
				};
			});
		};

		this.$el.classList.add("PierFormWrapper");

		this.$el.addEventListener(
			"autosave-reference-field",
			({ detail } = {}) => {
				if (this.values && this.values._id) {
					this.API.updateRecord(this.selectedModelName, {
						...detail,
						_id: this.values._id,
					});
				}
			}
		);
	},
	data: function () {
		return {
			savingReferenceModel: false,
			referenceModel: null,
			referenceModalResolver: null,
		};
	},
	methods: {
		onSave({ data }, isReferenceModel) {
			if (isReferenceModel) this.saveReferenceModal(data);
			else if (this.values) this.$store.dispatch("editRecord", data);
			else this.$store.dispatch("createRecord", data);
		},
		closeReferenceModal() {
			this.referenceModalResolver(null);
		},
		async saveReferenceModal(data) {
			try {
				this.savingReferenceModel = true;
				const res = await this.API.insertRecord(
					this.referenceModel.name,
					data
				);
				this.savingReferenceModel = false;
				this.referenceModalResolver(res);
			} catch (error) {
				this.savingReferenceModel = false;
				handleNetworkError(
					error,
					`Error adding to ${this.referenceModel.name}:`
				);
			}
		},
	},
	computed: {
		...mapState(["models", "savingRecord", "selectedModelName"]),
		...mapGetters(["selectedModel"]),
	},
};
