document.addEventListener('DOMContentLoaded', function() {
    // Preview image before upload
    const imageInput = document.getElementById('recipe_image');
    const previewContainer = document.createElement('div');
    previewContainer.className = 'mt-2';
    
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Remove any existing preview
                while (previewContainer.firstChild) {
                    previewContainer.removeChild(previewContainer.firstChild);
                }

                // Create preview image
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Recipe image preview';
                        img.className = 'img-thumbnail';
                        img.style.maxWidth = '200px';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        // Insert preview container after the file input
        imageInput.parentNode.insertBefore(previewContainer, imageInput.nextSibling);
    }

    // Auto-generate alt text from title
    const titleInput = document.getElementById('title');
    const altTextInput = document.getElementById('alt_text');
    
    if (titleInput && altTextInput) {
        titleInput.addEventListener('input', function() {
            // Only update alt text if it's empty or matches previous title
            const currentAlt = altTextInput.value;
            const previousTitle = titleInput.dataset.previousTitle || '';
            
            if (!currentAlt || currentAlt === previousTitle + ' recipe image') {
                altTextInput.value = this.value + ' recipe image';
            }
            
            // Store current title for next comparison
            titleInput.dataset.previousTitle = this.value;
        });
    }

    // Form validation
    const form = document.querySelector('.recipe-form form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            // Remove existing error messages
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            // Check each required field
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Add error message
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'This field is required';
                    field.parentNode.appendChild(feedback);
                }
            });

            // Validate time fields
            const timeFields = ['prep_hours', 'prep_minutes', 'cook_hours', 'cook_minutes'];
            timeFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field && field.value) {
                    const value = parseInt(field.value);
                    if (fieldId.includes('minutes') && (value < 0 || value > 59)) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Minutes must be between 0 and 59';
                        field.parentNode.appendChild(feedback);
                    }
                    if (value < 0) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'Time cannot be negative';
                        field.parentNode.appendChild(feedback);
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});
