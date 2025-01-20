document.addEventListener('DOMContentLoaded', function() {
    // Find all favorite buttons
    const favoriteButtons = document.querySelectorAll('.favorite-button');

    // Add click handler to each button
    favoriteButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault(); // Prevent any parent link from being clicked
            e.stopPropagation(); // Stop event from bubbling up

            const recipeId = this.dataset.recipeId;
            const isFavorited = this.classList.contains('favorited');

            try {
                // Create form data
                const formData = new FormData();
                formData.append('recipe_id', recipeId);

                // Send request to toggle favorite
                const response = await fetch('../api/toggle_favorite.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to toggle favorite');
                }

                // Toggle the favorited class
                this.classList.toggle('favorited');
                
                // Update the tooltip
                this.title = this.classList.contains('favorited') ? 'Remove from favorites' : 'Add to favorites';

                // Update the heart icon color
                const heartIcon = this.querySelector('i');
                if (heartIcon) {
                    heartIcon.style.color = this.classList.contains('favorited') ? '#ff4b4b' : '#999';
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Failed to update favorite status. Please try again.');
                
                // Revert the button state
                this.classList.toggle('favorited', isFavorited);
            }
        });

        // Prevent the recipe link from being clicked when clicking the favorite button
        button.addEventListener('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });
});
