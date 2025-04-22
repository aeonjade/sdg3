const uploadedFiles = {};
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

const allowedFormats = {
  "CTC-G11": [".pdf"],
  "CTC-G12": [".pdf"],
  "CTC-ID": [".pdf"],
  "Birth-Certificate": [".pdf"],
  "Applicant-Voter-Certificate": [".pdf"],
  "Parent-Voter-Certificate": [".pdf"],
  "ID-Picture": [".jpg", ".png"],
};

function triggerUpload(id) {
  document.getElementById(id).click();
}

function removeFile(id) {
  const input = document.getElementById(id);
  const preview = document.getElementById("preview-" + id);
  const container = input.closest(".upload-container");
  const uploadBtn = container.querySelector(".upload-btn");
  const errorMessage = container.querySelector(".error-message");

  input.value = "";
  preview.classList.add("hidden");

  if (uploadBtn) uploadBtn.style.display = "block";
  if (errorMessage) errorMessage.classList.add("hidden");

  const checklistIcon = document.querySelector(`#item-${id} .info`);
  const checkIcon = document.querySelector(`#item-${id} .check`);
  if (checklistIcon && checkIcon) {
    checklistIcon.style.display = "inline";
    checkIcon.style.display = "none";
  }

  delete uploadedFiles[id];
  checkAllFilesUploaded();
}

function checkAllFilesUploaded() {
  const checkIcons = document.querySelectorAll(".check");
  const submitBtn = document.querySelector(".submit-btn");

  const allUploaded = Array.from(checkIcons).every(
    (icon) => icon.style.display === "inline"
  );
  if (submitBtn) submitBtn.disabled = !allUploaded;
}
 
function isValidFile(id, file) {
  const extension = file.name.split(".").pop().toLowerCase();
  return allowedFormats[id]?.includes("." + extension);
}

function toggleChecklist() {
  const checklistContent = document.getElementById("checklistContent");
  const chevronIcon = document.getElementById("chevron-icon");

  // Toggle visibility
  checklistContent.classList.toggle("hidden");

  // Toggle chevron rotation
  chevronIcon.classList.toggle("rotate-180");
}

 
document.querySelectorAll(".file-input").forEach((input) => {
  input.addEventListener("change", function () {
    const id = this.id;
    const file = this.files[0];
    const preview = document.getElementById("preview-" + id);
    const container = input.closest(".upload-container");
    const uploadBtn = container.querySelector(".upload-btn");
    const errorMessage = container.querySelector(".error-message");

    if (errorMessage) {
      errorMessage.textContent = "";
      errorMessage.classList.remove("visible");
    }

    if (!file) return;

    if (!isValidFile(id, file)) {
      if (errorMessage) {
        errorMessage.textContent = `Invalid format. Allowed: ${allowedFormats[id].join(", ")}`;
        errorMessage.style.display = "block";
      }
      return;
    }

    if (file.size > MAX_FILE_SIZE) {
      if (errorMessage) {
        errorMessage.textContent = `File size exceeds ${MAX_FILE_SIZE / 1024 / 1024} MB.`;
        errorMessage.style.display = "block";
      }
      return;
    }

    // Display filename
    preview.querySelector(".file-name").textContent = file.name;
    preview.classList.remove("hidden");
    if (uploadBtn) uploadBtn.style.display = "none";

    // Checklist icon update
    const checklistIcon = document.querySelector(`#item-${id} .info`);
    const checkIcon = document.querySelector(`#item-${id} .check`);
    if (checklistIcon && checkIcon) {
      checklistIcon.style.display = "none";
      checkIcon.style.display = "inline";
    }

    // Save file
    uploadedFiles[id] = file;

    // Download icon
    const downloadIcon = preview.querySelector(".document-requirements-icon");
    if (downloadIcon) {
      downloadIcon.onclick = () => {
        const url = URL.createObjectURL(file);
        const link = document.createElement("a");
        link.href = url;
        link.download = file.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
      };
    }

    // View text
    const viewText = preview.querySelector(".view-text");
    if (viewText) {
      viewText.onclick = () => {
        const url = URL.createObjectURL(file);
        window.open(url, "_blank");
      };
    }

    checkAllFilesUploaded();
  });
});

/*Pasted from admin-documents php */
document.addEventListener('DOMContentLoaded', () => {
  let namefile;
  let currentRejectId = null;

  window.showRejectPopup = function(id, filename) {
    namefile = filename;
    currentRejectId = id;

    const popup = document.getElementById('rejectPopup');
    if (popup) {
      popup.classList.remove('hidden');
    }
  };

  window.cancelReject = function() {
    const popup = document.getElementById('rejectPopup');
    if (popup) {
      popup.classList.add('hidden');
    }
  };

  window.saveRejectMessage = function () {
    const message = document.getElementById('rejectMessageInput').value;
  
    const formData = new FormData();
    formData.append("fileName", namefile);
    formData.append("rejectReason", message);
  
    fetch("php/updateRejectMessage.php", {
      method: "POST",
      body: formData,
    }).then(res => res.text());
  
    cancelReject();
  
    const approveText = document.querySelector(`#actions-${currentRejectId} .approve-text`);
    const rejectText = document.querySelector(`#actions-${currentRejectId} .reject-text`);
    const rejectIcon = document.getElementById(`reject-icon-${currentRejectId}`);
    const approveIcon = document.getElementById(`approve-icon-${currentRejectId}`);
    const actionsDiv = document.getElementById(`actions-${currentRejectId}`);
  
    if (approveText) approveText.style.display = "none";
    if (rejectText) rejectText.style.display = "none";
  
    if (rejectIcon) rejectIcon.classList.remove("hidden");
    if (approveIcon) approveIcon.classList.add("hidden");
  
    if (actionsDiv) actionsDiv.setAttribute("data-status", "rejected");
  
    checkAllReviewed();
  };
  

  window.downloadSampleFile = function(filePath) {
    const link = document.createElement("a");
    link.href = filePath;
    link.download = filePath.split("/").pop();
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };
  
  window.handleDecision = function(id, action) {
    const approveText = document.querySelector(`#actions-${id} .approve-text`);
    const rejectText = document.querySelector(`#actions-${id} .reject-text`);
    const approveIcon = document.getElementById(`approve-icon-${id}`);
    const rejectIcon = document.getElementById(`reject-icon-${id}`);
    const actionsDiv = document.getElementById(`actions-${id}`);
  
    if (approveText) approveText.style.display = "none";
    if (rejectText) rejectText.style.display = "none";
  
    if (action === "approve") {
      if (approveIcon) approveIcon.classList.remove("hidden");
      if (rejectIcon) rejectIcon.classList.add("hidden");
      if (actionsDiv) actionsDiv.setAttribute("data-status", "approved");
    }
    checkAllReviewed();
  };
  

  const submitBtn = document.getElementById('submitBtn');
  const successPopup = document.getElementById('successPopup');

  if (submitBtn && successPopup) {
    submitBtn.addEventListener('click', () => {
      if (!submitBtn.disabled) {
        successPopup.classList.remove('hidden');
      }
    });
  }  
});

function checkAllReviewed() {
  const allReviewed = Array.from(document.querySelectorAll('[id^="actions-"]')).every(container => {
    const status = container.getAttribute('data-status');
    return status === 'approved' || status === 'rejected';
  });

  const submitBtn = document.getElementById('submitBtn');
  if (submitBtn) {
    if (allReviewed) {
      submitBtn.disabled = false;
      submitBtn.classList.remove('bg-[#c7acee]', 'text-[#6c6c6c]', 'cursor-not-allowed');
      submitBtn.classList.add('bg-[#6a11cb]', 'text-white', 'cursor-pointer', 'hover:bg-[#5a0eb5]');
    } else {
      submitBtn.disabled = true;
      submitBtn.classList.add('bg-[#c7acee]', 'text-[#6c6c6c]', 'cursor-not-allowed');
      submitBtn.classList.remove('bg-[#6a11cb]', 'text-white', 'cursor-pointer', 'hover:bg-[#5a0eb5]');
    }
  }
}

//All onclicks in admin-document
document.addEventListener('DOMContentLoaded', () => {
  const chevronIcon = document.getElementById('chevron-icon');
  const checklistContent = document.getElementById('checklistContent');

  if (chevronIcon) {
    chevronIcon.addEventListener('click', () => {
      checklistContent.classList.toggle('hidden');
      chevronIcon.classList.toggle('rotate-180');
    });
  }

  const reloadBtn = document.getElementById('reload-btn');
  if (reloadBtn) {
    reloadBtn.addEventListener('click', () => window.location.reload());
  }

  const saveReject = document.getElementById('save-reject');
  if (saveReject) {
    saveReject.addEventListener('click', saveRejectMessage);
  }

  const cancelRejectBtn = document.getElementById('cancel-reject');
  if (cancelRejectBtn) {
    cancelRejectBtn.addEventListener('click', cancelReject);
  }
});
