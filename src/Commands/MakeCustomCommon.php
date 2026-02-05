<?php
namespace Davion190510\MiniCRUDGenerator\Commands;

class MakeCustomCommon
{
    public function filterProjectName($names)
    {
        $newPosition = strpos($names, "~");
        $projectName = substr($names, $newPosition + 1);
        $projectPosition = strpos($projectName, '.');
        $finalProjectName = substr($projectName, 0, $projectPosition);
        return $finalProjectName;
    }

    public function filterFolderName($names)
    {
        $projectPosition = strpos($names, '.');
        $position = strpos($names, '/');
        $folderName = substr($names, 0, $position);
        $folderName = substr($folderName, $projectPosition + 1);
        return $folderName;
    }

    public function filterSubFolderName($names)
    {
        $position = strpos($names, '/');
        $subFolderName = substr($names, $position + 1, -1);
        return $subFolderName;
    }

    public function filterMainName($names)
    {
        $position = strpos($names, '/');
        $controllerPosition = strpos($names, '?');
        $controllerName = substr($names, 0, $controllerPosition);
        $controllerName = substr($controllerName, $position + 1);
        return $controllerName;
    }

    public function filterApiName($names)
    {
        $position = strpos($names, '=');
        $pathName = substr($names, $position + 1);
        return $pathName;
    }

    public function filterModuleName($names)
    {
        $position = strpos($names, '~');
        $moduleName = substr($names, 0, $position);
        return $moduleName;
    }
}
