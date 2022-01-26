// import * as htmlToImage from 'html-to-image';
// import { toPng, toJpeg, toBlob, toPixelData, toSvg } from 'html-to-image';

// htmlToImage.toPng(node)
//     .then(function (dataUrl) {
//         var img = new Image();
//         img.src = dataUrl;
//         document.body.appendChild(img);
//     })
//     .catch(function (error) {
//         console.error('oops, something went wrong!', error);
//     });

function exportBillet(ticket) {
    const apiUrl = 'http://localhost:8000';
    htmlToImage.toPng(document.getElementById(ticket))
        .then(function (dataUrl) {
            $.post(apiUrl + '/myzap/store-billet', {img: dataUrl}).done((result) => {
                console.log(result)
            }).fail((err) => {
                console.log(err)
            })
        });
}
