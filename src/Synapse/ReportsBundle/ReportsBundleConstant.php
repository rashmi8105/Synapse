<?php

namespace Synapse\ReportsBundle;

class ReportsBundleConstant
{
    const PDF_ZOOM = 1.042;

    // "4A" is the inverse of "A4" -> for Landscape
    const PDF_INVERSE = '4A';

    // 72 is the defacto standard for printer resolution. It's 72dpi so that the PDF paper size is correct based on what's rendered
    const PDF_DPI = 72;

    const PHANTOM_JS_PATH = '/usr/local/bin/phantomjs --web-security=false --ssl-protocol=tlsv12 ';

    const PDFIFY_JS = '/../pdfify.js ';

    //This minimum file size may change if we use something other than PhantomJS or if PhantomJS is updated
    const MINIMUM_STUDENT_SURVEY_REPORT_BYTE_SIZE = 41600;

    const GENERATED_REPORT_TOO_SMALL_MESSAGE = 'The generated file is too small to be correct. Skipping. Will retry later.';
}