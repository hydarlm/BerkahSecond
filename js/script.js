// Custom JavaScript for BerkahSecond

document.addEventListener('DOMContentLoaded', function () {
  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Initialize animations
  animateOnScroll();

  // File upload functionality
  initFileUpload();

  // Search functionality
  initSearch();

  // Price range slider
  initPriceRange();
});

// Animate elements on scroll
function animateOnScroll() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px',
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in');
      }
    });
  }, observerOptions);

  // Observe product cards
  document.querySelectorAll('.product-card').forEach((card) => {
    observer.observe(card);
  });
}

// File upload with drag and drop
function initFileUpload() {
  const fileUploadArea = document.querySelector('.file-upload-area');
  const fileInput = document.querySelector('#product_image');

  if (fileUploadArea && fileInput) {
    // Click to upload
    fileUploadArea.addEventListener('click', () => {
      fileInput.click();
    });

    // Drag and drop
    fileUploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', () => {
      fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      fileUploadArea.classList.remove('dragover');

      const files = e.dataTransfer.files;
      if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect(files[0]);
      }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
      }
    });
  }
}

// Handle file selection
function handleFileSelect(file) {
  const fileUploadArea = document.querySelector('.file-upload-area');
  const maxSize = 5 * 1024 * 1024; // 5MB

  if (file.size > maxSize) {
    showAlert('File terlalu besar! Maksimal 5MB.', 'danger');
    return;
  }

  if (!file.type.startsWith('image/')) {
    showAlert('File harus berupa gambar!', 'danger');
    return;
  }

  // Preview image
  const reader = new FileReader();
  reader.onload = function (e) {
    fileUploadArea.innerHTML = `
            <img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
            <p class="mt-2 mb-0 text-success">
                <i class="fas fa-check-circle me-2"></i>${file.name}
            </p>
        `;
  };
  reader.readAsDataURL(file);
}

// Search functionality
function initSearch() {
  const searchInput = document.querySelector('#search');
  const searchBtn = document.querySelector('#searchBtn');

  if (searchInput && searchBtn) {
    // Search on enter key
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
      }
    });

    // Search on button click
    searchBtn.addEventListener('click', performSearch);
  }
}

// Perform search
function performSearch() {
  const searchInput = document.querySelector('#search');
  const categorySelect = document.querySelector('#category');
  const sortSelect = document.querySelector('#sort');

  if (searchInput) {
    const params = new URLSearchParams();

    if (searchInput.value.trim()) {
      params.append('search', searchInput.value.trim());
    }

    if (categorySelect && categorySelect.value) {
      params.append('category', categorySelect.value);
    }

    if (sortSelect && sortSelect.value) {
      params.append('sort', sortSelect.value);
    }

    // Redirect to catalog with search parameters
    window.location.href = 'katalog.php?' + params.toString();
  }
}

// Price range functionality
function initPriceRange() {
  const minPrice = document.querySelector('#min_price');
  const maxPrice = document.querySelector('#max_price');
  const applyFilter = document.querySelector('#applyFilter');

  if (minPrice && maxPrice && applyFilter) {
    applyFilter.addEventListener('click', () => {
      const params = new URLSearchParams(window.location.search);

      if (minPrice.value) {
        params.set('min_price', minPrice.value);
      } else {
        params.delete('min_price');
      }

      if (maxPrice.value) {
        params.set('max_price', maxPrice.value);
      } else {
        params.delete('max_price');
      }

      window.location.href = 'katalog.php?' + params.toString();
    });
  }
}

// Show alert function
function showAlert(message, type = 'info') {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
  alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';

  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

  document.body.appendChild(alertDiv);

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (alertDiv.parentNode) {
      alertDiv.remove();
    }
  }, 5000);
}

// Format number to Rupiah
function formatRupiah(number) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(number);
}

// Loading state for buttons
function setButtonLoading(button, loading = true) {
  if (loading) {
    button.disabled = true;
    const originalText = button.innerHTML;
    button.setAttribute('data-original-text', originalText);
    button.innerHTML = '<span class="loading me-2"></span>Loading...';
  } else {
    button.disabled = false;
    const originalText = button.getAttribute('data-original-text');
    if (originalText) {
      button.innerHTML = originalText;
    }
  }
}

// Smooth scroll to element
function scrollToElement(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
    });
  }
}

// Copy to clipboard
function copyToClipboard(text) {
  navigator.clipboard
    .writeText(text)
    .then(() => {
      showAlert('Berhasil disalin ke clipboard!', 'success');
    })
    .catch(() => {
      showAlert('Gagal menyalin ke clipboard!', 'danger');
    });
}

// Confirm dialog
function confirmAction(message, callback) {
  if (confirm(message)) {
    callback();
  }
}

// Image lazy loading
function initLazyLoading() {
  const images = document.querySelectorAll('img[data-src]');

  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        imageObserver.unobserve(img);
      }
    });
  });

  images.forEach((img) => imageObserver.observe(img));
}
