<?php
namespace Application\Domain\Util\FileSystem;

use Cake\Filesystem\File;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MimeType
{

    const IMAGE_TYPE = [
        "jpeg",
        "jpeg jpg",
        "jpg",
        "png",
        "bmp",
        "gif",
        "webp"
    ];

    const COMMON_TYPE = [
        "audio/aac" => [
            "description" => "AAC audio",
            "extention" => "aac"
        ],
        "application/x-abiword" => [
            "description" => "AbiWord document",
            "extention" => "abw"
        ],
        "application/x-freearc" => [
            "description" => "Archive document (multiple files embedded)",
            "extention" => "arc"
        ],
        "video/x-msvideo" => [
            "description" => "AVI: Audio Video Interleave",
            "extention" => "avi"
        ],
        "application/vnd.amazon.ebook" => [
            "description" => "Amazon Kindle eBook format",
            "extention" => "azw"
        ],
        "application/octet-stream" => [
            "description" => "Any kind of binary data",
            "extention" => "bin"
        ],
        "image/bmp" => [
            "description" => "Windows OS/2 Bitmap Graphics",
            "extention" => "bmp"
        ],
        "application/x-bzip" => [
            "description" => "BZip archive",
            "extention" => "bz"
        ],
        "application/x-bzip2" => [
            "description" => "BZip2 archive",
            "extention" => "bz2"
        ],
        "application/x-cdf" => [
            "description" => "CD audio",
            "extention" => "cda"
        ],
        "application/x-csh" => [
            "description" => "C-Shell script",
            "extention" => "csh"
        ],
        "text/css" => [
            "description" => "Cascading Style Sheets (CSS)",
            "extention" => "css"
        ],
        "text/csv" => [
            "description" => "Comma-separated values (CSV)",
            "extention" => "csv"
        ],
        "application/msword" => [
            "description" => "Microsoft Word",
            "extention" => "doc"
        ],
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => [
            "description" => "Microsoft Word (OpenXML)",
            "extention" => "docx"
        ],
        "application/vnd.ms-fontobject" => [
            "description" => "MS Embedded OpenType fonts",
            "extention" => "eot"
        ],
        "application/epub+zip" => [
            "description" => "Electronic publication (EPUB)",
            "extention" => "epub"
        ],
        "application/gzip" => [
            "description" => "GZip Compressed Archive",
            "extention" => "gz"
        ],
        "image/gif" => [
            "description" => "Graphics Interchange Format (GIF)",
            "extention" => "gif"
        ],
        "text/html" => [
            "description" => "HyperText Markup Language (HTML)",
            "extention" => "htm html"
        ],
        "image/vnd.microsoft.icon" => [
            "description" => "Icon format",
            "extention" => "ico"
        ],
        "text/calendar" => [
            "description" => "iCalendar format",
            "extention" => "ics"
        ],
        "application/java-archive" => [
            "description" => "Java Archive (JAR)",
            "extention" => "jar"
        ],
        "image/jpeg" => [
            "description" => "JPEG images",
            "extention" => "jpeg"
        ],
        "text/javascript (Specifications: HTML and its reasoning, and IETF)" => [
            "description" => "JavaScript",
            "extention" => "js"
        ],
        "application/json" => [
            "description" => "JSON format",
            "extention" => "json"
        ],
        "application/ld+json" => [
            "description" => "JSON-LD format",
            "extention" => "jsonld"
        ],
        "audio/midi audio/x-midi" => [
            "description" => "Musical Instrument Digital Interface (MIDI)",
            "extention" => "mid midi"
        ],
        "text/javascript" => [
            "description" => "JavaScript module",
            "extention" => "mjs"
        ],
        "audio/mpeg" => [
            "description" => "MP3 audio",
            "extention" => "mp3"
        ],
        "video/mp4" => [
            "description" => "MP4 video",
            "extention" => "mp4"
        ],
        "video/mpeg" => [
            "description" => "MPEG Video",
            "extention" => "mpeg"
        ],
        "application/vnd.apple.installer+xml" => [
            "description" => "Apple Installer Package",
            "extention" => "mpkg"
        ],
        "application/vnd.oasis.opendocument.presentation" => [
            "description" => "OpenDocument presentation document",
            "extention" => "odp"
        ],
        "application/vnd.oasis.opendocument.spreadsheet" => [
            "description" => "OpenDocument spreadsheet document",
            "extention" => "ods"
        ],
        "application/vnd.oasis.opendocument.text" => [
            "description" => "OpenDocument text document",
            "extention" => "odt"
        ],
        "audio/ogg" => [
            "description" => "OGG audio",
            "extention" => "oga"
        ],
        "video/ogg" => [
            "description" => "OGG video",
            "extention" => "ogv"
        ],
        "application/ogg" => [
            "description" => "OGG",
            "extention" => "ogx"
        ],
        "audio/opus" => [
            "description" => "Opus audio",
            "extention" => "opus"
        ],
        "font/otf" => [
            "description" => "OpenType font",
            "extention" => "otf"
        ],
        "image/png" => [
            "description" => "Portable Network Graphics",
            "extention" => "png"
        ],
        "application/pdf" => [
            "description" => "Adobe Portable Document Format (PDF)",
            "extention" => "pdf"
        ],
        "application/x-httpd-php" => [
            "description" => "Hypertext Preprocessor (Personal Home Page)",
            "extention" => "php"
        ],
        "application/vnd.ms-powerpoint" => [
            "description" => "Microsoft PowerPoint",
            "extention" => "ppt"
        ],
        "application/vnd.openxmlformats-officedocument.presentationml.presentation" => [
            "description" => "Microsoft PowerPoint (OpenXML)",
            "extention" => "pptx"
        ],
        "application/vnd.rar" => [
            "description" => "RAR archive",
            "extention" => "rar"
        ],
        "application/rtf" => [
            "description" => "Rich Text Format (RTF)",
            "extention" => "rtf"
        ],
        "application/x-sh" => [
            "description" => "Bourne shell script",
            "extention" => "sh"
        ],
        "image/svg+xml" => [
            "description" => "Scalable Vector Graphics (SVG)",
            "extention" => "svg"
        ],
        "application/x-shockwave-flash" => [
            "description" => "Small web format (SWF) or Adobe Flash document",
            "extention" => "swf"
        ],
        "application/x-tar" => [
            "description" => "Tape Archive (TAR)",
            "extention" => "tar"
        ],
        "image/tiff" => [
            "description" => "Tagged Image File Format (TIFF)",
            "extention" => "tif tiff"
        ],
        "video/mp2t" => [
            "description" => "MPEG transport stream",
            "extention" => "ts"
        ],
        "font/ttf" => [
            "description" => "TrueType Font",
            "extention" => "ttf"
        ],
        "text/plain" => [
            "description" => "Text, (generally ASCII or ISO 8859-n)",
            "extention" => "txt"
        ],
        "application/vnd.visio" => [
            "description" => "Microsoft Visio",
            "extention" => "vsd"
        ],
        "audio/wav" => [
            "description" => "Waveform Audio Format",
            "extention" => "wav"
        ],
        "audio/webm" => [
            "description" => "WEBM audio",
            "extention" => "weba"
        ],
        "video/webm" => [
            "description" => "WEBM video",
            "extention" => "webm"
        ],
        "image/webp" => [
            "description" => "WEBP image",
            "extention" => "webp"
        ],
        "font/woff" => [
            "description" => "Web Open Font Format (WOFF)",
            "extention" => "woff"
        ],
        "font/woff2" => [
            "description" => "Web Open Font Format (WOFF)",
            "extention" => "woff2"
        ],
        "application/xhtml+xml" => [
            "description" => "XHTML",
            "extention" => "xhtml"
        ],
        "application/vnd.ms-excel" => [
            "description" => "Microsoft Excel",
            "extention" => "xls"
        ],
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => [
            "description" => "Microsoft Excel (OpenXML)",
            "extention" => "xlsx"
        ],
        "application/vnd.mozilla.xul+xml" => [
            "description" => "XUL",
            "extention" => "xul"
        ],
        "application/zip" => [
            "description" => "ZIP archive",
            "extention" => "zip"
        ],
        "video/3gpp; audio/3gpp if it doesn't contain video" => [
            "description" => "3GPP audio/video container",
            "extention" => "3gp"
        ],
        "video/3gpp2; audio/3gpp2 if it doesn't contain video" => [
            "description" => "3GPP2 audio/video container",
            "extention" => "3g2"
        ],
        "application/x-7z-compressed" => [
            "description" => "7-zip archive",
            "extention" => "7z"
        ]
    ];

    static public function get($file_type)
    {
        return self::COMMON_TYPE[$file_type];
    }

    static public function isImage1($file_type)
    {
        $e = self::COMMON_TYPE[$file_type];

        // var_dump($e);

        if (isset($e["extention"])) {
            return in_array($e["extention"], self::IMAGE_TYPE);
        }
        return false;
    }

    static public function isPicture($file_type)
    {
        if (preg_match('(image)', $file_type)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $path
     * @return boolean
     */
    static public function isImage($path)
    {
        $file = new File($path);
        if (! $file->exists()) {
            return false;
        }
        $file_type = $file->mime();

        if (! $file_type) {
            return false;
        }

        return self::isPicture($file_type);
    }
}


