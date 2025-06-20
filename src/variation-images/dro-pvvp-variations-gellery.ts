
/**
 * Manages variation image galleries with thumbnail navigation.
 * @since 1.1.0
 */
class DROPVVP_VariationGallery {
    private mainImage: HTMLImageElement;
    private thumbnailsContainer: HTMLElement;
    private variationSelects: NodeListOf<HTMLSelectElement>;
    private variationsData: Record<number, { thumbnail: string; large: string }[]>;

    constructor() {
        this.init();
    }

    /**
     * Initialize the gallery.
     */
    private init(): void {
        this.cacheDOM();
        this.bindEvents();
    }

    /**
     * Cache DOM elements.
     */
    private cacheDOM(): void {
        this.mainImage = document.getElementById('dro-pvvp-main') as HTMLImageElement;
        this.thumbnailsContainer = document.querySelector('.dro-pvvp-thumbnails')!;
        this.variationSelects = document.querySelectorAll('.variations select');
        this.variationsData = JSON.parse(
            document.getElementById('dro-pvvp-variation-data')?.textContent || '{}'
        );
    }

    /**
     * Bind event listeners.
     */
    private bindEvents(): void {
        this.variationSelects.forEach(select => {
            select.addEventListener('change', () => this.handleVariationChange());
        });

        this.thumbnailsContainer.addEventListener('click', (e) => {
            const thumb = e.target as HTMLImageElement;
            if (thumb.classList.contains('dro-pvvp-thumb')) {
                this.mainImage.src = thumb.dataset.large!;
            }
        });
    }

    /**
     * Handle variation change event.
     */
    private handleVariationChange(): void {
        const variationId = parseInt((this.variationSelects[0]?.value || '0'));
        if (!variationId || !this.variationsData[variationId]) return;

        this.updateGallery(this.variationsData[variationId]);
    }

    /**
     * Update the gallery with new images.
     */
    private updateGallery(images: { thumbnail: string; large: string }[]): void {
        this.thumbnailsContainer.innerHTML = '';
        images.forEach(img => {
            const thumb = document.createElement('img');
            thumb.className = 'dro-pvvp-thumb';
            thumb.src = img.thumbnail;
            thumb.dataset.large = img.large;
            thumb.loading = 'lazy';
            this.thumbnailsContainer.appendChild(thumb);
        });

        if (images.length > 0) {
            this.mainImage.src = images[0].large;
        }
    }
}

// Initialize
new DROPVVP_VariationGallery();