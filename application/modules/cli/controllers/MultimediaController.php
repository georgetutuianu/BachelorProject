<?php
/**
 * Description of IndexController
 *
 * @author George Tutuianu <george.tzutzu@gmail.com>
 * @year 2014
 */
class Cli_MultimediaController extends Utility_Controller_CliGeneric
{
    public function downloadVideoAction()
    { 
        set_time_limit(0);
        $cronId = md5(time());
        require 'WindowsAzure/WindowsAzure.php';
        $multimediaService = new Service_Multimedia();
        
        $videoToConvert = $multimediaService->getVideoToConvert();
        $multimediaService->markAsInProgress($videoToConvert['id']);
        
        try {
            $audioFileName = $multimediaService->downloadVideo($videoToConvert, $cronId);
            $filePath = realpath(sprintf(
                '%s/../data/downloads/%s', APPLICATION_PATH, $audioFileName
            ));
            
            $multimediaService->storeBlobInAzure($filePath, $audioFileName);
            $multimediaService->setBlobName($audioFileName, $videoToConvert['id']);
        } catch (Exception $exception) {
            $multimediaService->markAsFailed($videoToConvert['id']);
            echo $exception->getMessage();
        }
        
        $multimediaService->collectGarbage($cronId);
    }
}
