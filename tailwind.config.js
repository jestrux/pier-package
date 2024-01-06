module.exports = {
	content: [
		"./resources/**/*.{blade,.html,.php}",
		"./src/**/*.{blade,.html,.php}",
	],
	theme: {
		extend: {
			colors: {
				canvas: "rgb(var(--canvas-color) / <alpha-value>)",
				card: "rgb(var(--card-color) / <alpha-value>)",
				content: "rgb(var(--content-color) / <alpha-value>)",
				primary: "rgb(var(--primary-color) / <alpha-value>)",
			},
		},
	},
};
