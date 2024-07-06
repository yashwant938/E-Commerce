
const prevBtns = document.querySelectorAll(".btn-prev");
const nextBtns = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".progress-step");



let formStepsNum = 0;

Dropzone.options.dropzone = {

    url: "/upload.php",
    maxFilesize: 5, // 5MB
    acceptedFiles: "image/*,application/pdf", 
    autoProcessQueue: true,
    dictDefaultMessage: "Drop files here or click to upload",
    dictFallbackMessage: "Your browser does not support drag and drop file uploads.",
    dictInvalidFileType: "Invalid file type. Please upload an image or PDF.",
    dictFileTooBig: "File is too big ({{filesize}}MB). Max file size: {{maxFilesize}}MB.",
    dictResponseError: "Server responded with {{statusCode}} code.",
  };
  
nextBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    console.log("hello");
    formStepsNum++;
    updateFormSteps();
    updateProgressbar();
  });
});

prevBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    formStepsNum--;
    updateFormSteps();
    updateProgressbar();
  });
});

function updateFormSteps() {
  formSteps.forEach((formStep) => {
    formStep.classList.contains("form-step-active") &&
      formStep.classList.remove("form-step-active");
  });

  formSteps[formStepsNum].classList.add("form-step-active");
}

function updateProgressbar() {
  progressSteps.forEach((progressStep, idx) => {
    if (idx < formStepsNum + 1) {
      progressStep.classList.add("progress-step-active");
    } else {
      progressStep.classList.remove("progress-step-active");
    }
  });

  const progressActive = document.querySelectorAll(".progress-step-active");

  progress.style.width =
    ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%";
}
