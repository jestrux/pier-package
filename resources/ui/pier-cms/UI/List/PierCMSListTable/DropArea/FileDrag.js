import EM from 'EventEmitter';

function FileDrag(el) {
    this.em = new EM();

    el.addEventListener("dragover", (e) => this.FileDragHover(e), false);
    el.addEventListener("dragleave", (e) => this.FileDragHover(e), false);
    el.addEventListener("drop", (e) => this.FileSelectHandler(e), false);

    return this;
}

FileDrag.prototype.FileDragHover = function(e){
    e.stopPropagation();
    e.preventDefault();
    if (e.type == "dragover")
        e.target.classList.add("hover");
    else
        e.target.classList.remove("hover");
}

FileDrag.prototype.FileSelectHandler = function(e){
    e.stopPropagation();
    e.preventDefault();

    this.FileDragHover(e);

    this.em.emit("selected", e);

    // var files = e.target.files || e.dataTransfer.files;

    // if(!files || !files.length)
    //     return;

    // let file = files[0];

    // this.em.emit("complete", true, file);

    // var reader = new FileReader();
    // reader.onload = (e) => {
    //     this.em.emit("loaded", file, e.target.result);
    //     this.UploadFile(file);
    // }
    // reader.readAsDataURL(file);
}

export default FileDrag;