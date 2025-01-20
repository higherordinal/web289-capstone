document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scroll behavior for the "Back to Recipes" link
    const backLink = document.querySelector('.back-link');
    if (backLink) {
        backLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            setTimeout(() => {
                window.location = this.href;
            }, 300);
        });
    }

    // Add hover effect for recipe meta items
    const metaItems = document.querySelectorAll('.recipe-meta span');
    metaItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.color = 'var(--color-primary)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.color = '';
        });
    });

    // Initialize video container aspect ratio
    const videoContainer = document.querySelector('.video-container');
    if (videoContainer) {
        const iframe = videoContainer.querySelector('iframe');
        if (iframe) {
            // Set 16:9 aspect ratio
            videoContainer.style.paddingBottom = '56.25%';
        }
    }

    // Add print recipe functionality only if print button doesn't exist
    if (!document.querySelector('.print-recipe-btn')) {
        const printButton = document.createElement('button');
        printButton.className = 'print-recipe-btn';
        printButton.innerHTML = '<i class="fas fa-print"></i> Print Recipe';
        
        // Insert print button after recipe description
        const recipeDescription = document.querySelector('.recipe-description');
        if (recipeDescription) {
            recipeDescription.parentNode.insertBefore(printButton, recipeDescription.nextSibling);
        }

        // Add print functionality
        printButton.addEventListener('click', function() {
            window.print();
        });
    }

    // Add CSS for print button and print layout
    const existingStyle = document.querySelector('#recipe-show-styles');
    if (!existingStyle) {
        const style = document.createElement('style');
        style.id = 'recipe-show-styles';
        style.textContent = `
            .print-recipe-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin: 1rem 0;
                padding: 0.75rem 1.5rem;
                background-color: var(--color-gray-700);
                color: var(--color-white);
                border: none;
                border-radius: var(--radius-sm);
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .print-recipe-btn:hover {
                background-color: var(--color-gray-800);
                transform: translateY(-1px);
            }

            @media print {
                .back-link, 
                .print-recipe-btn,
                .scaling-buttons,
                .comments-section,
                footer {
                    display: none !important;
                }

                .recipe-show {
                    margin: 0;
                    padding: 0;
                }

                .recipe-header-image {
                    max-height: 300px;
                }

                .recipe-ingredients,
                .recipe-directions {
                    break-inside: avoid;
                    page-break-inside: avoid;
                    margin: 1rem 0;
                    padding: 1rem 0;
                    box-shadow: none;
                }
            }
        `;
        document.head.appendChild(style);
    }
});
