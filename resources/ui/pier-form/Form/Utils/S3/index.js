import Signature from "./Signature";
import Policy from "./Policy";
import { xAmzDate, dateYMD } from "./Date";
import { throwError } from './ErrorThrower';
import axios from 'axios';

const baseConfig = {
    bucketName: process.env.MIX_S3_BUCKET,
    region: process.env.MIX_S3_REGION,
    accessKeyId: process.env.MIX_S3_ACCESS_KEY_ID,
    secretAccessKey: process.env.MIX_S3_SECRET_ACCESS_KEY,
};

class S3FileUpload {
    static async uploadFile(file, overridenConfig) {
        const config = {
            ...baseConfig,
            ...overridenConfig
        };

        // Error Thrower :x:
        throwError(config, file);

        const fd = new FormData();
        const key = `${config.dirName ? config.dirName + "/" : ""}${file.name}`;
        const url = `https://${config.bucketName}.s3.amazonaws.com/`;
        fd.append("key", key);
        fd.append("acl", "public-read");
        fd.append("Content-Type", file.type);
        fd.append("x-amz-meta-uuid", "14365123651274");
        fd.append("x-amz-server-side-encryption", "AES256");
        fd.append("X-Amz-Credential", `${config.accessKeyId}/${dateYMD}/${config.region}/s3/aws4_request`);
        fd.append("X-Amz-Algorithm", "AWS4-HMAC-SHA256");
        fd.append("X-Amz-Date", xAmzDate);
        fd.append("x-amz-meta-tag", "");
        fd.append("Policy", Policy.getPolicy(config));
        fd.append("X-Amz-Signature", Signature.getSignature(config, dateYMD, Policy.getPolicy(config)));
        fd.append("file", file);

        const axiosConfig = {
            url,
            method: "post",
            headers: {
                fd
            },
            data: fd,
            onUploadProgress: p => config.onProgress(p.loaded * 100 / p.total)
        };
        
        try {
            const data = await axios(axiosConfig);
            return Promise.resolve({
                bucket: config.bucketName,
                key: `${config.dirName ? config.dirName + "/" : ""}${file.name}`,
                location: `${url}${config.dirName ? config.dirName + "/" : ""}${file.name}`,
                result: data
            });
        } 
        catch (err) {
            return Promise.reject(err);        
        }
    }
    static async deleteFile(fileName, config) {
        const fd = new FormData();
        const url = `https://${config.bucketName}.s3-${config.region}.amazonaws.com/${config.dirName ? config.dirName + "/" : ""}${fileName}`;
        fd.append("Date", xAmzDate);
        fd.append("X-Amz-Date", xAmzDate);
        fd.append("Authorization", Signature.getSignature(config, dateYMD, Policy.getPolicy(config)));
        fd.append("Content-Type", "text/plain");

        const params = {
            method: "delete",
            headers: {
                fd
            }
        };

        const deleteResult = await fetch(url, params);
        if (!deleteResult.ok) return Promise.reject(deleteResult);
        return Promise.resolve({
            ok: deleteResult.ok,
            status: deleteResult.status,
            message: "File Deleted",
            fileName: fileName
        });
    }
}

const { uploadFile, deleteFile } = S3FileUpload;

export { uploadFile, deleteFile };

export default S3FileUpload;