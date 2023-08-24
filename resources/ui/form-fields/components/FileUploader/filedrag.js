import axios from "axios";
import EM from "EventEmitter";
import S3FileUpload from "../../../Utils/S3";
import { showErrorToast } from "../../../Utils";

function FileDrag(el, { url, s3, type, maxSize = 10 * 1000000 } = {}) {
	this.fileType = type;
	this.maxSize = maxSize;
	this.em = new EM();
	this.upload_path = url;
	this.s3Config = s3;
	const input = el.querySelector("input");

	if (input) {
		input.addEventListener("change", (e) => {
			this.FileSelectHandler(e);
		});
	}

	el.addEventListener("dragover", (e) => this.FileDragHover(e), false);
	el.addEventListener("dragleave", (e) => this.FileDragHover(e), false);
	el.addEventListener("drop", (e) => this.FileSelectHandler(e), false);

	return this;
}

FileDrag.prototype.FileDragHover = function (e) {
	e.stopPropagation();
	e.preventDefault();
	if (e.type == "dragover") e.target.classList.add("hover");
	else e.target.classList.remove("hover");
};

FileDrag.prototype.FileSelectHandler = function (e) {
	e.stopPropagation();
	e.preventDefault();

	this.FileDragHover(e);

	var files = e.target.files || e.dataTransfer.files;

	if (!files || !files.length) return;

	let file = files[0];

	let supportedFileTypes = [
		"xlsx",
		"xls",
		"csv",
		"tab",
		"tsv",
		"spreadsheet",
		"excel",
		"pdf",
		"doc",
		"docx",
		"ppt",
		// Images
		"png",
		"svg",
		"jpg",
		"jpeg",
		"webp",
		"gif",
		// Videos
		"mp4",
		"webm",
		"mov",
		// Misc
		"json",
	];

	if (this.fileType == "image") {
		supportedFileTypes = ["png", "svg", "jpg", "jpeg", "webp", "gif"];
	}

	const typeSupported =
		!file.type || !file.type.length
			? null
			: supportedFileTypes.find((type) => file.type.indexOf(type) != -1);

	if (!typeSupported) return showErrorToast("Invalid file type.");

	const fileSize = files[0].size; // file size in bytes
	if (fileSize > this.maxSize) {
		return showErrorToast(
			`File is too large. Max file size is ${this.maxSize / 1000000}Mbs.`
		);
	}

	var reader = new FileReader();
	reader.onload = (e) => {
		this.em.emit("loaded", file, e.target.result);
		this.UploadFile(file);
	};
	reader.readAsDataURL(file);
};

FileDrag.prototype.UploadFile = function (file) {
	// && file.size <= $id("MAX_FILE_SIZE").value
	// if (xhr.upload && file.type == "image/jpeg") {
	if (this.upload_path == "s3") {
		S3FileUpload.uploadFile(file, {
			...(this.s3Config || {}),
			onProgress: (percent) => {
				this.em.emit("progressed", percent);
			},
		})
			.then(({ location }) => {
				console.log("File location: ", location);
				this.em.emit("complete", true, location);
			})
			.catch((error) => {
				console.log("Error uploadign file");
				this.em.emit(
					"complete",
					false,
					"A network or server error occured!"
				);
			});

		return;
	}

	const config = {
		headers: { "content-type": "multipart/form-data" },
		onUploadProgress: (progressEvent) => {
			var per = (progressEvent.loaded * 100) / progressEvent.total;
			// console.log("Progress: ", per);
			this.em.emit("progressed", per);
		},
	};
	var form = new FormData();
	var name = file.name.replace(/ /g, "-");
	var ext = name.split(".").pop();
	form.append("photo", file);
	form.append("name", name);
	form.append("ext", ext);
	form.append("fileType", this.fileType);

	axios
		.post(this.upload_path, form, config)
		.then((result) => {
			const res = result.data;
			const payload = res.success ? res.path : res.msg;
			this.em.emit("complete", res.success, payload);
			if (!res.success) showErrorToast(payload);
		})
		.catch((e) => {
			console.log("Error uploading file: ", e);
			this.em.emit(
				"complete",
				false,
				"A network or server error occured!"
			);
			showErrorToast("An unknown error occured!");
		});
};

export default FileDrag;
