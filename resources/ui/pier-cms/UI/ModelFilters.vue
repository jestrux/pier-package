<style scoped>
.popover-custom {
  width: 340px;
  right: 0 !important;
  left: auto !important;
  background: #fff;
  border: 1px solid #ddd;
  /* border-radius: 5px; */
}

/* .popover-custom select {
  background: #fff
    url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100' fill='%23999'><polygon points='0,0 100,0 50,50'/></svg>")
    no-repeat scroll 96.5% 60%;
  background-size: 12px;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  border: none !important;
} */
</style>

<style>
.ModelFilterField .autocomplete-input {
  margin: 0 !important;
  padding: 0.35rem 0.4rem !important;
  height: auto !important;
  background: transparent;
  border-radius: 3px !important;
  min-width: 170px !important;
}

.ModelFilterField .autocomplete-input + ul {
  border-top-color: rgba(0,0,0,.12) !important;
  margin-top: 6px !important;
  border-radius: 8px !important;
  min-width: 220px;
}

.ModelFilterField .autocomplete-input + ul li {
  padding-left: 1rem;
  padding-right: 1rem;
  background-image: none;
}
</style>

<script>
import Popper from "popper.js";
import { mapActions, mapState } from "vuex";
import ModelAutoComplete from "../../components/ModelAutoComplete.vue";

Popper.Defaults.modifiers.computeStyle.gpuAcceleration = false;

const randomId = () => Math.random().toString(36).substring(7);

export default {
  name: "ModelFilters",
  props: ["model"],
  mounted() {
    this.$nextTick(function () {
      this.popper = new Popper(
        this.$refs["showFiltersButton"],
        this.$refs["filterDropdown"],
        {
          // placement: 'bottom-left'
        }
      );
    });

    document.addEventListener("click", (e) => {
      const el = e.target;
      const withinTrigger = el.closest("#showFiltersButton");
      const withinDropdown = el.closest("#filterDropdown");

      if (!withinTrigger && !withinDropdown) this.showFilters = false;
    });

    document.addEventListener("keyup", (e) => {
      if (e.key === "Escape") this.showFilters = false;
    });
  },
  data() {
    return {
      filtersKey: randomId(),
      popper: null,
      showFilters: false,
    };
  },
  computed: {
    ...mapState(["modelFilters"]),
  },
  methods: {
    ...mapActions(["setFilter"]),
  },
  components: {
    ModelAutoComplete,
  },
  render(h) {
    const renderFilter = (field) => {
      switch (field.type) {
        case "status": {
          return (
            <select
              class="border p-1 rounded w-full"
              value={this.modelFilters?.[field.label]}
              onChange={(e) =>
                this.setFilter({ [field.label]: e.target.value })
              }
            >
              <option value="">All</option>
              {field.meta.availableStatuses.map((status) => (
                <option value={status.name}>{status.name}</option>
              ))}
            </select>
          );
        }

        case "boolean": {
          return (
            <select
              style=""
              class="border p-1 rounded w-full"
              value={this.modelFilters?.[field.label]}
              onChange={(e) =>
                this.setFilter({ [field.label]: e.target.value })
              }
            >
              <option value="">All</option>
              <option value="1">True</option>
              <option value="0">False</option>
            </select>
          );
        }

        case "reference": {
          return (
            <div key={field._id}>
              <ModelAutoComplete
                modelName={field.meta.model}
                modelMainField={field.meta.mainField}
                placeholder={`Type to search...`}
                v-model={this.modelFilters?.[field.label]}
                onInput={({ _id }) => this.setFilter({ [field.label]: _id })}
              />
            </div>
          );
        }

        default:
          return null;
      }
    };

    if (!this.model) return;

    const filters = this.model.fields
      .filter((field) =>
        ["status", "boolean", "reference"].includes(field.type)
      )
      .map((f) => ({ ...f, _id: randomId() }));

    const clearedFilters = [...JSON.parse(JSON.stringify(filters))].reduce(
      (agg, field) => {
        return {
          ...agg,
          [field.label]: "",
        };
      },
      {}
    );

    return (
      <div class="relative" style={{ display: !filters.length ? "none" : "" }}>
        <button
          ref="showFiltersButton"
          id="showFiltersButton"
          style="display: flex"
          class="rounded-md border-2 py-1 px-2 flex items-center focus:outline-none"
          onClick={() => (this.showFilters = !this.showFilters)}
        >
          <svg fill="#888" width="24" height="24" viewBox="0 0 24 24">
            <path d="M7,6h10l-5.01,6.3L7,6z M4.25,5.61C6.27,8.2,10,13,10,13v6c0,0.55,0.45,1,1,1h2c0.55,0,1-0.45,1-1v-6 c0,0,3.72-4.8,5.74-7.39C20.25,4.95,19.78,4,18.95,4H5.04C4.21,4,3.74,4.95,4.25,5.61z" />
          </svg>

          <svg class="h-4 w-4 ml-1" viewBox="0 0 24 24">
            <path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z" />
          </svg>
        </button>

        <div
          id="filterDropdown"
          ref="filterDropdown"
          class="popover-custom shadow-md rounded-md"
          style={{ display: this.showFilters ? "" : "none" }}
        >
          <div key={this.filtersKey} class="p-3 flex flex-col gap-2">
            {filters.map((field) => {
              return (
                <div class="ModelFilterField flex items-center justify-between gap-3">
                  <span class="inline-block first-letter:uppercase flex-1">
                    {field.label ? field.label.replace(/_/g, " ") : ""}
                  </span>
                  <div
                    key={field._id}
                    class="flex-shrink-0"
                    style="width: 180px"
                  >
                    {renderFilter(field)}
                  </div>
                </div>
              );
            })}
          </div>

          <div class="h-8 flex items-center px-3 bg-neutral-100 rounded-b-md">
            <button
              class="underline text-xs font-semibold"
              onClick={() => {
                this.setFilter(clearedFilters);

                this.$nextTick(() => {
                  this.filtersKey = randomId();
                });
              }}
            >
              Reset filters
            </button>
          </div>
        </div>
      </div>
    );
  },
};
</script>