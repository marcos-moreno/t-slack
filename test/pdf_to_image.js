const b64 = await this.request(this.path,{'action' : 'select_file_item',"model":file}); 
var pdfData = atob(b64);
// console.log(pdfData);

//   Loaded via <script> tag, create shortcut to access PDF.js exports.
var pdfjsLib = window['pdfjs-dist/build/pdf'];
//   console.log(pdfjsLib);
// The workerSrc property shall be specified.
pdfjsLib.GlobalWorkerOptions.workerSrc = 'http://mozilla.github.io/pdf.js/build/pdf.worker.js';

// Using DocumentInitParameters object to load binary data.
var loadingTask = pdfjsLib.getDocument({data: pdfData});
loadingTask.promise.then(function(pdf) {
console.log('PDF loaded'); 
// Fetch the first page
var pageNumber = 1;
pdf.getPage(pageNumber).then(function(page) {
    console.log('Page loaded');
    
    var scale = 1.5;
    var viewport = page.getViewport({scale: scale});

    // Prepare canvas using PDF page dimensions
    var canvas = document.getElementById('the-canvas');
    var context = canvas.getContext('2d');
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Render PDF page into canvas context
    var renderContext = {
    canvasContext: context,
    viewport: viewport
    };
    var renderTask = page.render(renderContext);
    renderTask.promise.then(function () {
    console.log('Page rendered');
    });
});
}, function (reason) {
// PDF loading error
console.error(reason);
});
