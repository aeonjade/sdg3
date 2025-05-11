// Define sample image paths (Ensure images exist in the "samples" folder)
const sampleImages = {
  "CTC-G11": "assets/samples/CTC-G11.jpg",
  "CTC-G12": "assets/samples/CTC-G12.jpg",
  "CTC-ID": "assets/samples/CTC-ID.jpg",
  "Birth-Certificate": "assets/samples/Birth-Certificate.jpg",
  "Applicant-Voter-Certificate": "assets/samples/Parent-Voter-Certificate.jpg",
  "Parent-Voter-Certificate": "assets/samples/Parent-Voter-Certificate.jpg",
  "ID-Picture": "assets/samples/ID-Picture.jpg",
};

function openSampleImage(documentType) {
  if (sampleImages[documentType]) {
    // Create modal elements
    const modal = document.createElement("div");
    modal.className =
      "popup fixed top-2/4 left-2/4 -translate-x-1/2 -translate-y-1/2 bg-[linear-gradient(to_bottom,_#b57ee4,_#a56ee0)] px-16 py-14 text-center rounded-3xl [box-shadow:0_0px_10px_rgba(0,_0,_0,_0.2)] flex flex-col items-center flex-[1] z-50";

    // Create modal content
    modal.innerHTML = `
          <div class="relative w-full max-w-3xl">
            <h2 class="m-0 text-2xl text-white mb-4">${documentType}</h2>
            <img 
              src="${sampleImages[documentType]}" 
              alt="Sample ${documentType}" 
              class="max-w-full max-h-[70vh] object-contain rounded-lg"
            >
            <button 
              class="absolute -top-8 -right-8 bg-[rgb(145,_29,_52)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 px-4 py-2 hover:bg-[#0C5AAD]">
              Close
            </button>
          </div>
        `;

    // Add click handler to close button
    const closeButton = modal.querySelector("button");
    closeButton.addEventListener("click", () => {
      document.body.removeChild(modal);
      document.body.removeChild(overlay);
    });

    // Add dark overlay
    const overlay = document.createElement("div");
    overlay.className = "fixed inset-0 bg-black bg-opacity-50 z-40";
    document.body.appendChild(overlay);

    // Add modal to body
    document.body.appendChild(modal);
  } else {
    alert("Sample image not available.");
  }
}

// Global variables for reject popup
let currentDocType = "";
let currentFileName = "";

// Function to show reject popup
function showRejectPopup(documentType, fileName) {
  currentDocType = documentType;
  currentFileName = fileName;
  const rejectPopup = document.getElementById("rejectPopup");
  const rejectMessageInput = document.getElementById("rejectMessageInput");
  rejectMessageInput.value = ""; // Clear previous message
  rejectPopup.classList.remove("hidden");
}

// Add this function at the top level of your file
function showConfirmDialog(title, message, onConfirm) {
  const modal = document.createElement("div");
  modal.className = "fixed inset-0 z-50 flex items-center justify-center";
  modal.innerHTML = `
        <div class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="bg-[linear-gradient(to_bottom,_#b57ee4,_#a56ee0)] p-8 rounded-3xl shadow-xl text-center relative z-10 w-[90%] max-w-md">
            <h2 class="text-2xl font-bold text-white mb-4">${title}</h2>
            <p class="text-white mb-8">${message}</p>
            <div class="flex justify-center gap-4">
                <button class="cancel-btn bg-[rgb(145,_29,_52)] px-6 py-2 rounded-xl text-white font-bold hover:bg-[#0C5AAD] transition">Cancel</button>
                <button class="confirm-btn bg-[rgb(45,_174,_40)] px-6 py-2 rounded-xl text-white font-bold hover:bg-[#0C5AAD] transition">Confirm</button>
            </div>
        </div>
    `;

  document.body.appendChild(modal);

  return new Promise((resolve) => {
    modal.querySelector(".confirm-btn").addEventListener("click", () => {
      document.body.removeChild(modal);
      resolve(true);
    });

    modal.querySelector(".cancel-btn").addEventListener("click", () => {
      document.body.removeChild(modal);
      resolve(false);
    });
  });
}

// Modify the handleDecision function
async function handleDecision(documentType, decision) {
  if (decision === "approve") {
    const confirmed = await showConfirmDialog(
      "Confirm Approval",
      "Are you sure you want to approve this document?"
    );

    if (!confirmed) return;
  }

  const actionsDiv = document.getElementById(`actions-${documentType}`);
  const rejectIcon = document.getElementById(`reject-icon-${documentType}`);
  const approveIcon = document.getElementById(`approve-icon-${documentType}`);
  const rejectText = actionsDiv.querySelector(".reject-text");
  const approveText = actionsDiv.querySelector(".approve-text");

  // Get applicantID from the URL or data attribute
  const applicantID = document.body.dataset.applicantId || 1;

  if (decision === "approve") {
    // Send approval to server
    fetch("php/approveDocument.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `documentType=${documentType}&applicantID=${applicantID}`,
    })
      .then((response) => response.text())
      .then(() => {
        // Update UI
        rejectIcon.classList.add("hidden");
        approveIcon.classList.remove("hidden");
        rejectText.classList.add("hidden");
        approveText.classList.add("hidden");
        actionsDiv.dataset.status = "approved";

        // Remove any existing reject message
        const rejectMessage =
          actionsDiv.parentElement.querySelector(".text-[red]");
        if (rejectMessage) {
          rejectMessage.remove();
        }

        // Update checklist icon
        const checklistItem = document.getElementById(`item-${documentType}`);
        if (checklistItem) {
          const statusIcon = checklistItem.querySelector("img");
          statusIcon.src = "assets/Check-Icon.png";
        }

        updateSubmitButton();
      })
      .catch((error) => console.error("Error:", error));
  }
}

// Checklist toggle functionality
document.addEventListener("DOMContentLoaded", function () {
  const checklistHeader = document.getElementById("checklist-header");
  const checklistContent = document.getElementById("checklistContent");
  const chevronIcon = document.getElementById("chevron-icon");
  let isOpen = true;

  function toggleChecklist() {
    isOpen = !isOpen;

    // Toggle content
    if (!isOpen) {
      checklistContent.style.maxHeight = "0";
      checklistContent.style.opacity = "0";
      chevronIcon.style.transform = "rotate(180deg)";
    } else {
      checklistContent.style.maxHeight = "500px";
      checklistContent.style.opacity = "1";
      chevronIcon.style.transform = "rotate(0deg)";
    }
  }

  // Add click event to header
  checklistHeader.addEventListener("click", toggleChecklist);

  // Add reject popup event listeners
  const saveRejectBtn = document.getElementById("saveRejectBtn");
  const cancelRejectBtn = document.getElementById("cancelRejectBtn");
  const rejectPopup = document.getElementById("rejectPopup");

  saveRejectBtn.addEventListener("click", function () {
    const rejectMessage = document.getElementById("rejectMessageInput").value;
    const applicantID = document.body.dataset.applicantId || 1;

    if (rejectMessage.trim() === "") {
      alert("Please enter a reason for rejection");
      return;
    }

    // Send reject message to server
    fetch("php/updateRejectMessage.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `documentType=${currentDocType}&rejectReason=${encodeURIComponent(
        rejectMessage
      )}&applicantID=${applicantID}`,
    })
      .then((response) => response.text())
      .then(() => {
        // Update UI
        const actionsDiv = document.getElementById(`actions-${currentDocType}`);
        const rejectIcon = document.getElementById(
          `reject-icon-${currentDocType}`
        );
        const approveIcon = document.getElementById(
          `approve-icon-${currentDocType}`
        );
        const rejectText = actionsDiv.querySelector(".reject-text");
        const approveText = actionsDiv.querySelector(".approve-text");

        // Remove any existing reject message first
        const existingMessage =
          actionsDiv.parentElement.querySelector(".text-[red]");
        if (existingMessage) {
          existingMessage.remove();
        }

        rejectIcon.classList.remove("hidden");
        approveIcon.classList.add("hidden");
        rejectText.classList.add("hidden");
        approveText.classList.add("hidden");
        actionsDiv.dataset.status = "rejected";

        // Add reject message
        const messageElement = document.createElement("p");
        messageElement.className = "text-[red] font-medium mt-4 ml-2";
        messageElement.textContent = "Reason for rejection: " + rejectMessage;

        // Get the document container and append the message
        const documentContainer = actionsDiv.closest(".document-container");
        if (documentContainer) {
          documentContainer.appendChild(messageElement);
        }

        // Update checklist icon
        const checklistItem = document.getElementById(`item-${currentDocType}`);
        if (checklistItem) {
          const statusIcon = checklistItem.querySelector("img");
          statusIcon.src = "assets/Wrong-Icon.png";
        }

        // Hide popup
        const rejectPopup = document.getElementById("rejectPopup");
        rejectPopup.classList.add("hidden");

        // Add overlay removal
        const overlay = document.querySelector(".bg-black.bg-opacity-50");
        if (overlay) {
          overlay.remove();
        }

        updateSubmitButton();
      })
      .catch((error) => console.error("Error:", error));
  });

  cancelRejectBtn.addEventListener("click", function () {
    rejectPopup.classList.add("hidden");
  });

  updateChecklistStatus();

  // Add submit button handler
  const submitBtn = document.getElementById("submitBtn");
  submitBtn.addEventListener("click", async function () {
    if (!this.disabled) {
      const confirmed = await showConfirmDialog(
        "Confirm Submission",
        "Are you sure you want to submit? This action cannot be undone."
      );

      if (confirmed) {
        window.location.href = "application-tracking.php";
      }
    }
  });

  updateSubmitButton();
});

// Add hover effect for better UX
document
  .getElementById("checklist-header")
  .addEventListener("mouseenter", function () {
    this.style.opacity = "0.9";
  });

document
  .getElementById("checklist-header")
  .addEventListener("mouseleave", function () {
    this.style.opacity = "1";
  });

// Add a function to update all checklist items based on document status
function updateChecklistStatus() {
  const actionDivs = document.querySelectorAll("[id^='actions-']");
  actionDivs.forEach((div) => {
    const documentType = div.id.replace("actions-", "");
    const status = div.dataset.status;
    const checklistItem = document.getElementById(`item-${documentType}`);

    if (checklistItem) {
      const statusIcon = checklistItem.querySelector("img");
      if (status === "approved") {
        statusIcon.src = "assets/Check-Icon.png";
      } else if (status === "rejected") {
        statusIcon.src = "assets/Wrong-Icon.png";
      } else if (status === "pending") {
        statusIcon.src = "assets/Info-Icon.png";
      }
    }
  });
}

function updateSubmitButton() {
  const submitBtn = document.getElementById("submitBtn");
  const actionDivs = document.querySelectorAll("[id^='actions-']");
  let hasPendingDocuments = false;

  actionDivs.forEach((div) => {
    if (div.dataset.status === "pending") {
      hasPendingDocuments = true;
    }
  });

  if (!hasPendingDocuments) {
    // Enable button and update styling
    submitBtn.disabled = false;
    submitBtn.classList.remove("cursor-not-allowed", "text-[gray]");
    submitBtn.classList.add(
      "cursor-pointer",
      "text-[white]",
      "bg-[#7213D0]",
      "hover:bg-[white]",
      "hover:text-[black]"
    );
  } else {
    // Disable button and reset styling
    submitBtn.disabled = true;
    submitBtn.classList.add("cursor-not-allowed", "text-[gray]");
    submitBtn.classList.remove(
      "cursor-pointer",
      "text-[white]",
      "bg-[#7213D0]",
      "hover:bg-[white]",
      "hover:text-[black]"
    );
  }
}
