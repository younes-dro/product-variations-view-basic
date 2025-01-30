// types.d.ts
declare namespace wp {
    function template(name: string): (data: any) => string;

    function media(attributes: any): wp.media.view.MediaFrame; // Declare `wp.media` as a callable function

    namespace media {
        var editor: any;

        namespace view {
            class MediaFrame {
                open(): void;
                on(event: string, callback: Function): void;
                state(): any;
            }

            namespace MediaFrame {
                class Select extends MediaFrame { }
                class Post extends MediaFrame { }
                class Manage extends MediaFrame { }
                class ImageDetails extends MediaFrame { }
                class AudioDetails extends MediaFrame { }
                class VideoDetails extends MediaFrame { }
            }
        }
    }
}

interface ImageSize {
    url: string;
}

interface ImageSizes {
    thumbnail?: ImageSize;
    full: ImageSize;
}

interface Image {
    type: string;
    id: number;
    sizes: ImageSizes;
}

type MediaFrame =
    | wp.media.view.MediaFrame.Select
    | wp.media.view.MediaFrame.Post
    | wp.media.view.MediaFrame.Manage
    | wp.media.view.MediaFrame.ImageDetails
    | wp.media.view.MediaFrame.AudioDetails
    | wp.media.view.MediaFrame.VideoDetails
    | wp.media.view.MediaFrame.EditAttachments;

// type ImageSizes = {
//     full: string;
//     medium?: string;
//     thumbnail?: string;
//   };

