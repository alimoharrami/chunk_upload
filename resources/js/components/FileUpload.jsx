import React, { useState } from 'react';
import { FilePond, registerPlugin } from "react-filepond";
import "filepond/dist/filepond.min.css";
import FilePondPluginFileValidateSize from "filepond-plugin-file-validate-size";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";

import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css";

registerPlugin(
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview
);

const FileUpload = () => {
    const [files, setFiles] = useState([]);
    const [fileName, setFileName] = useState('');

    const server = {
        url: `/api/chunk-upload`,
    };

    function handleFile(isCancel = false) {
        if (isCancel) {
            setFiles([]);
        } else {
            setFileName(document.querySelector('input[name="chunkfile"]').getAttribute("value"))
        }
    }

    return (
        <div className="w-2/3 ml-auto mr-auto">
            <h2 className="m-5 text-center text-2xl ">Chunk Upload</h2>
            <FilePond
                className="[&_div.filepond--drop-label]:light:!bg-gray-200 [&_div.filepond--drop-label]:!rounded-md"
                files={files}
                onupdatefiles={setFiles}
                chunkUploads={true}
                onprocessfiles={handleFile}
                onremovefile={() => handleFile(true)}
                allowMultiple={false}
                chunkSize={50 * 1024}
                maxFileSize={5 * 1024 * 1024}
                acceptedFileTypes={["image/jpeg", "image/png"]}
                server={server}
                name="chunkfile"
                labelIdle='Drag & Drop your files or <span class="filepond--label-action">Browse</span>'
            />
        </div>
    );
};

export default FileUpload;
