function Slider(container, options) {
    const defaultOptions = {
      cycle: false,
      hideActions: false,
      showMarkers: false,
      showActionsOnHover: false,
      slideDuration: 2000,
      onChange: null,
    };
    
    this.options = {
      ...defaultOptions,
      ...options
    };

    this.colors = [];
    this.markers = [];
    this.wrapper = this.setupSlider(container);
    this.Scroller = this.wrapper.querySelector(".SlideScroller");
    this.SlideItems = this.wrapper.querySelectorAll(".SlideItem");
    this.SlideItems[0].classList.add("current");
    this.ActiveSlide = this.SlideItems[0];
    this.currentIndex = 0;
  
    if(this.options.showActionsOnHover)
      this.wrapper.classList.add("show-actions-on-hover");
  
    if(!this.options.hideActions)
      this.addMovers();
  
    if(this.options.showMarkers)
      this.addMarkers();
  
    return this;
  }
  
  Slider.prototype.setupSlider = function(container) {
    Array.from(container.children).forEach(node => {
      const color = node.getAttribute("color");
      if(color)
        this.colors.push(color);

      node.classList.add("SlideItem");
    });
  
    const SliderContent = document.createElement("div");
    SliderContent.classList.add("Slider");
  
    container.classList.add("SlideScroller");
    container.parentNode.insertBefore(SliderContent, container);
    SliderContent.appendChild(container);
  
    const SlideContainer = document.createElement("div");
    SlideContainer.classList.add("SlideContainer");
  
    SliderContent.insertBefore(SlideContainer, container);
    SlideContainer.appendChild(container);
  
    return SliderContent;
  }
  
  Slider.prototype.scrollSliderBack = function () {
    if(this.currentIndex > 0)
      this.currentIndex -= 1;
    else if(this.options.cycle)
      this.currentIndex = this.SlideItems.length - 1;
    else
      return;
      
    this.updateUI();
    if(this.slideshowTimer)
      this.play();
  };
  
  Slider.prototype.scrollSliderForward = function (fromAutoPlay) {
    if(this.currentIndex < this.SlideItems.length - 1)
      this.currentIndex += 1;
    else if(this.options.cycle)
      this.currentIndex = 0;
    else
      return;
      
    this.updateUI();
  
    if(fromAutoPlay !== true){
      if(this.slideshowTimer){
        clearInterval(this.slideshowTimer);
        this.play();
      }
    }
  };
  
  Slider.prototype.scrollSliderTo = function (index) {
    if(index < 0 || index >= this.SlideItems.length)
      return;
  
    this.currentIndex = index;
      
    this.updateUI();
  
    if(this.slideshowTimer){
      clearInterval(this.slideshowTimer);
      this.play();
    }
  };
  
  Slider.prototype.updateUI = function () {
    this.setActiveSlide();
  
    if(this.markers.length){
      const activeMarker = this.markers.find((marker => marker.classList.contains("active")));
      if(activeMarker)
        activeMarker.classList.remove("active");
  
      this.markers[this.currentIndex].classList.add("active");
    }
  
    if(this.options.hideActions)
      return;
  
    if(!this.options.cycle){
      if(this.currentIndex === 0)
        this.prevMoverButton.classList.add('disabled');
      else
        this.prevMoverButton.classList.remove('disabled');
      
      if(this.currentIndex === this.SlideItems.length - 1)
        this.nextMoverButton.classList.add('disabled');
      else
        this.nextMoverButton.classList.remove('disabled');
    }
    else {
      this.prevMoverButton.classList.remove('disabled');
      this.nextMoverButton.classList.remove('disabled');
    }
  };
  
  Slider.prototype.setActiveSlide = function () {
    const slideWidth = 100;
    this.Scroller.style.transform = `translateX(${this.currentIndex * -slideWidth}%)`;
    this.ActiveSlide.classList.remove('current');
    const newActiveSlide = this.SlideItems[this.currentIndex];
    newActiveSlide.classList.add('current');
    this.ActiveSlide = newActiveSlide;
  
    if (typeof this.options.onChange === 'function'){
      const newPercent = this.currentIndex * 100 / (this.SlideItems.length - 1);
      this.options.onChange(this.currentIndex, newPercent);
    }
  };
  
  
  Slider.prototype.addMovers = function () {
    const moversHtml = `
      <div class="SlideMovers">
        <button aria-label="Previous">
          <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg>
        </button>
        
        <button aria-label="Next">
          <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
        </button>
      </div>
    `;
  
    const movers = new DOMParser().parseFromString(moversHtml, "text/html").body.firstChild;
    const moverButtons = movers.querySelectorAll("button");
  
    this.prevMoverButton = moverButtons[0];
    this.nextMoverButton = moverButtons[1];
  
    this.prevMoverButton.onclick = this.scrollSliderBack.bind(this);
    this.nextMoverButton.onclick = this.scrollSliderForward.bind(this);
  
    if(!this.options.cycle)
      this.prevMoverButton.classList.add("disabled");
  
    this.wrapper.appendChild(movers);
  };
  
  Slider.prototype.addMarkers = function () {
    const markers = document.createElement("div");
    markers.classList.add("SlideMarkers");
  
    this.SlideItems.forEach((_, index) => {
      const marker = document.createElement("button");
      if(this.colors && this.colors.length > index)
        marker.style.color = this.colors[index];

      marker.onclick = () => this.scrollSliderTo(index);
  
      if(index === this.currentIndex) 
        marker.classList.add("active");
  
      markers.appendChild(marker);
      this.markers.push(marker);
    });
  
    this.wrapper.appendChild(markers);
  };
  
  Slider.prototype.play = function (duration) {
    if(duration)
      this.options.slideDuration = duration;
    else
      duration = this.options.slideDuration;
  
    this.options.cycle = true;
  
    this.slideshowTimer = setInterval(() => {
      this.scrollSliderForward(true);
    }, duration);
  };
  
  export default Slider;