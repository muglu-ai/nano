document.addEventListener("DOMContentLoaded", function() {
  const editButtons = document.querySelectorAll(".edit-btn");
  const modal = document.querySelector(".modal-content");
  const modalTitle = document.getElementById("modalTitle");
  const form = document.getElementById("sponsorForm");

  const nameInput = document.getElementById("itemName");
  const imageInput = document.getElementById("itemImage");
  const descInput = document.getElementById("itemDescription");
  const statusSelect = document.getElementById("itemStatus");
  const quantityInput = document.getElementById("itemQuantity");
  const memPriceInput = document.getElementById("memberPrice");
  const regularPriceInput = document.getElementById("regularPrice");
  const ItemIdInput = document.getElementById("itemNo");

  // Open modal on edit button click
  editButtons.forEach(btn => {
    btn.addEventListener("click", function() {
      const row = this.closest("tr");

      // Set modal title
      modalTitle.textContent = "Edit Sponsor Item";

      // Populate form fields with existing data
      // console.log(this.dataset.id || row.dataset.id || "");
      nameInput.value = row.dataset.name || "";
      descInput.value = row.dataset.description || "";
      statusSelect.value = row.dataset.status?.toLowerCase() || "active";
      quantityInput.value = row.dataset.noItems || 1;
      memPriceInput.value = row.dataset.mem_price || 0;
      regularPriceInput.value = row.dataset.price || 0;
      ItemIdInput.value = this.dataset.id || row.dataset.id || "";

      // Optionally clear image (can't pre-fill file input for security reasons)
      // If the data-image attribute is present, use it; otherwise, leave blank
      imageInput.value = row.dataset.image || "";

      document.querySelector(".file-name").textContent = "No file chosen";

      // Show modal (you can add a class or use a modal library)
      modal.parentElement.style.display = "block";
    });
  });

  // Close modal on cancel
  document.getElementById("cancelBtn").addEventListener("click", () => {
    modal.parentElement.style.display = "none";
    form.reset();
  });

  // Close modal on clicking close button (X)
  document.querySelector(".close-btn").addEventListener("click", () => {
    modal.parentElement.style.display = "none";
    form.reset();
  });
});

document.addEventListener("DOMContentLoaded", function() {
  // Modal elements
  const sponsorModal = document.getElementById("sponsorModal");
  const imagePreviewModal = document.getElementById("imagePreviewModal");
  const overlay = document.getElementById("overlay");
  const modalTitle = document.getElementById("modalTitle");
  const sponsorForm = document.getElementById("sponsorForm");

  // Buttons
  const addNewBtn = document.getElementById("addNewBtn");
  const cancelBtn = document.getElementById("cancelBtn");
  const closeBtns = document.querySelectorAll(".close-btn");

  // File upload
  const fileInput = document.getElementById("itemImage");
  const fileName = document.querySelector(".file-name");

  // Image preview
  const previewImage = document.getElementById("previewImage");
  const viewBtns = document.querySelectorAll(".view-btn");

  // Table actions
  const editBtns = document.querySelectorAll(".edit-btn");
  const deleteBtns = document.querySelectorAll(".delete-btn");

  // Pagination
  const pageNumbers = document.querySelectorAll(".page-number");

  // Open add new sponsor modal
  addNewBtn.addEventListener("click", function() {
    modalTitle.textContent = "Add New Sponsor Item";
    sponsorForm.reset();
    fileName.textContent = "No file chosen";
    openModal(sponsorModal);
  });

  // Close modals
  closeBtns.forEach(btn => {
    btn.addEventListener("click", function() {
      closeAllModals();
    });
  });

  // Cancel button
  cancelBtn.addEventListener("click", function() {
    closeAllModals();
  });

  // File input change
  fileInput.addEventListener("change", function() {
    if (this.files.length > 0) {
      fileName.textContent = this.files[0].name;
    } else {
      fileName.textContent = "No file chosen";
    }
  });

  // View image buttons
  viewBtns.forEach(btn => {
    btn.addEventListener("click", function(e) {
      e.stopPropagation();
      const imgSrc = this.closest(".item-image").querySelector("img").src;
      previewImage.src = imgSrc;
      openModal(imagePreviewModal);
    });
  });

  // Edit buttons
  editBtns.forEach(btn => {
    btn.addEventListener("click", function() {
      const row = this.closest("tr");
      const name = row.cells[0].textContent;

      //get the item id from the data attribute
      const itemId = this.dataset.id || row.dataset.id || "";
      const imageUrl = row.dataset.image || "";

      // Set form values based on the row data
      document.getElementById("itemName").value = name.trim();
      document.getElementById("itemStatus").value = "active";
      document.getElementById("itemQuantity").value = row.cells[4].textContent;

      // Set item ID and image URL
      document.getElementById("itemNo").value = itemId;
      document.getElementById("itemImage").value = imageUrl;
      document.querySelector(".file-name").textContent = imageUrl ? imageUrl.split("/").pop() : "No file chosen";

      // Extract price values (remove currency symbol and commas)
      const memberPrice = row.cells[5].textContent.replace("₹", "").replace(",", "");
      const regularPrice = row.cells[6].textContent.replace("₹", "").replace(",", "");

      document.getElementById("memberPrice").value = parseInt(memberPrice, 10) || 0;
      document.getElementById("regularPrice").value = regularPrice;

      // Set description (simplified)
      const descriptionList = row.cells[2].querySelector(".description-list");
      if (descriptionList) {
        const descriptionText = Array.from(descriptionList.querySelectorAll("li"))
          .map(li => li.textContent)
          .join("\n");
        document.getElementById("itemDescription").value = descriptionText;
      }

      modalTitle.textContent = "Edit Sponsor Item";
      openModal(sponsorModal);
    });
  });

  // Delete buttons
  deleteBtns.forEach(btn => {
    btn.addEventListener("click", function() {
      if (confirm("Are you sure you want to delete this sponsor item?")) {
        const row = this.closest("tr");
        row.classList.add("fade-out");

        // Simulate deletion
       

        //send delete request to server
        // Try to get itemId from data attributes, fallback to a cell value if needed
        let itemId = this.dataset.id || row.dataset.id;
        if (!itemId && row.cells.length > 0) {
          // Try to get data-id from the first cell (where your <td> has data-id)
          const firstCell = row.cells[0];
          if (firstCell && firstCell.hasAttribute("data-id")) {
            itemId = firstCell.getAttribute("data-id");
          } else {
            itemId = firstCell ? firstCell.textContent.trim() : "";
          }
        }
       
        if (!itemId) {
          alert("Invalid item ID. Cannot delete sponsor item.");
          return;
        }
        const endpoint2 = `/sponsor-items/${itemId}/inactive`; 
        console.log("Endpoint: ", endpoint2);
        fetch(endpoint2, {
          method: "PUT",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
          }
        })
          .then(response => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.json();
          })
          .then(data => {
            alert("Sponsor item deleted successfully!");
          })
          .catch(error => {
            //console.error("There was a problem with the fetch operation:", error);
            alert("Failed to delete sponsor item.");
          });

           setTimeout(() => {
          row.remove();
        }, 300);
      }
    });
  });

  // Form submission
  sponsorForm.addEventListener("submit", function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    const itemId = formData.get("itemNo");

    //console.log("Form data:", Object.fromEntries(formData.entries()));


   
    const endpoint = itemId
      ? `/sponsor-items/${itemId}/update` // Laravel route: sponsor.update
      : "/sponsor-items/store"; // Create route

    const method = itemId ? "PUT" : "POST";

    console.log("Endpoint: ", endpoint);
    console.log("Method: ", method);

    fetch(endpoint, {
      method: method,
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
      .then(response => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then(data => {
        alert(`Sponsor item ${itemId ? "updated" : "created"} successfully!`);
        form.reset();
        closeAllModals();
        // Optionally, refresh the table or update row dynamically
      })
      .catch(error => {
        console.log("body", formData);
        console.error("There was a problem with the fetch operation:", error);
        alert("Failed to save sponsor item.");
      });
    alert("Sponsor item saved successfully!");
    closeAllModals();
  });
  // sponsorForm.addEventListener('submit', function(e) {
  //     e.preventDefault();
  //     console.log('Form submitted');

  //     //console all the form data
  //     // Ensure all form fields have a 'name' attribute in your HTML, e.g. <input id="itemName" name="itemName" ...>
  //     const formData = new FormData(sponsorForm);
  //     const data = {};
  //     for (const [key, value] of formData.entries()) {
  //         console.log(`${key}: ${value}`);
  //         data[key] = value;
  //     }
  //     // Now `data` is a plain object with all form values
  //     // console.log('Form data object:', data);

  //     // Here you would normally send the data to the server
  //     // For demo purposes, we'll just show a success message
  //     alert('Sponsor item saved successfully!');
  //     closeAllModals();
  // });

  // Pagination
  pageNumbers.forEach(btn => {
    btn.addEventListener("click", function() {
      pageNumbers.forEach(b => b.classList.remove("active"));
      this.classList.add("active");

      // Here you would normally fetch the data for the selected page
    });
  });

  // Search functionality
  const searchBtn = document.getElementById("addNewBtn");
  const searchInput = document.getElementById("addNewBtn");

  searchBtn.addEventListener("click", function() {
    const searchTerm = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll("#sponsorTable tbody tr");

    rows.forEach(row => {
      const name = row.cells[0].textContent.toLowerCase();
      const description = row.cells[2].textContent.toLowerCase();

      if (name.includes(searchTerm) || description.includes(searchTerm)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });

  // Enter key for search
  searchInput.addEventListener("keyup", function(e) {
    if (e.key === "Enter") {
      searchBtn.click();
    }
  });

  // Helper functions
  function openModal(modal) {
    overlay.style.display = "block";
    modal.style.display = "block";
    document.body.style.overflow = "hidden";
  }

  function closeAllModals() {
    sponsorModal.style.display = "none";
    imagePreviewModal.style.display = "none";
    overlay.style.display = "none";
    document.body.style.overflow = "";
  }

  // Close modal when clicking outside
  overlay.addEventListener("click", closeAllModals);

  // Add CSS for fade-out animation
  const style = document.createElement("style");
  style.textContent = `
        .fade-out {
            opacity: 0;
            transition: opacity 0.3s;
        }
    `;
  document.head.appendChild(style);
});
