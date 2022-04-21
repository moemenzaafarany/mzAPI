<?php
/* 1.0.0 */
class mzFiles
{
    //====================================//
    public static function scan(string $path, bool $deep_scan = false, bool $get_size = false): mzRes
    {
        try {
            $path = realpath($path);
            $array = [];
            if (!file_exists($path)) throw new Exception("path_doesnot_exist");
            if (!is_dir($path)) {
                $array = [mzFiles::fileDetails($path, $deep_scan == false, $get_size)];
                return new mzRes(200, null, null, $array);
            }
            //
            $array = mzFiles::recurringScan($path, $deep_scan, $get_size);
            $array = [mzFiles::fileDetails($path, $deep_scan == false, $get_size)] + $array;
            if ($deep_scan == true &&  $get_size == true) {
                foreach ($array as $i => $item) {
                    if ($item['type'] == "dir") {
                        foreach ($array as $item2) {
                            if ($item['path'] != $item2['path'] && strpos($item2['path'], $item['path']) !== false) {
                                $array[$i]['size'] += $item2['size'];
                            }
                        }
                    }
                }
            }
            return new mzRes(200, null, null, $array);
        } catch (Exception $e) {
            return new mzRes(500, "scan_failed=$e");
        }
    }
    //====================================//
    private static function recurringScan(string $path, bool $deep_scan = false, bool $get_size = false, array &$array = []): array
    {
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        $files = array_diff(scandir($path), array('..', '.'));
        foreach ($files as $file) {
            $fPath = $path . DIRECTORY_SEPARATOR . $file;
            //
            array_push($array, mzFiles::fileDetails($fPath, $deep_scan == false, $get_size));
            //
            if (is_dir($fPath) && $deep_scan == true) mzFiles::recurringScan($fPath, $deep_scan, $get_size, $array);
            //
        }
        return $array;
    }
    //====================================//
    private static function fileDetails(string $path, bool $size = false, bool $get_size = false): array
    {
        $arr = [];
        $arr['path'] = $path;
        $arr['dirname'] = dirname($path);
        $arr['basename'] = basename($path);
        $arr['type'] = filetype($path);
        $arr['mime'] = mime_content_type($path);
        if ($get_size == true) $arr['size'] = ($size == true ? mzFiles::getSize($path) : filesize($path));
        $arr['modified'] = filemtime($path);
        return $arr;
    }
    //====================================//
    private static function getSize(string $path, int &$size = 0): int
    {
        $size += filesize($path);
        if (is_dir($path)) {
            $directory = new \RecursiveDirectoryIterator($path, \FilesystemIterator::FOLLOW_SYMLINKS);
            $iterator = new \RecursiveIteratorIterator($directory);
            foreach ($iterator as $item) $size += $item->getSize();
        }
        return $size;
    }
    //====================================//
    public static function file(string $path, bool $create = false): mzRes
    {
        try {
            if (!is_file($path)) {
                $dir = dirname($path);
                $new = false;
                if (!is_dir($dir)) {
                    $res = mzFiles::folder($dir, $create);
                    if ($res->status != 200) return $res;
                    $new = true;
                }
                if ($create == true) {
                    if (!fopen($path, "w")) throw new Exception("cannot_create_file");
                } else throw new Exception("file_doesnot_exist");
            }
            return new mzRes(200, null, null, realpath($path));
        } catch (Exception $e) {
            if ($new == true) rmdir($dir);
            return new mzRes(500, "file_error=$e");
        }
    }
    //====================================//
    public static function folder(string $path, bool $create = false): mzRes
    {
        try {
            if (!is_dir($path)) {
                if ($create == true) {
                    if (!mkdir($path, 0777, true)) throw new Exception("cannot_create_folder");
                } else throw new Exception("folder_doesnot_exist");
            }
            return new mzRes(200, realpath($path));
        } catch (Exception $e) {
            return new mzRes(500, "folder_error=$e");
        }
    }
    //====================================//
    public static function delete(string $path): mzRes
    {
        try {
            if (is_file($path)) {
                if (unlink($path)) return new mzRes(200);
                throw new Exception("cannot_delete_file ('$path')");
            }
            if (is_dir($path)) {
                $array = mzFiles::recurringDelete($path);
                if (empty($array)) return new mzRes(200);
                throw new Exception("cannot_delete_files ('" . implode("','", $array) . "')");
            }
            return new mzRes(400, null, "path_doesnot_exist", null);
        } catch (Exception $e) {
            return new mzRes(500, "delete_failed=$e");
        }
    }
    //====================================//
    private static function recurringDelete(string $path, array &$array = []): array
    {
        $array = [];
        $path = rtrim($path, DIRECTORY_SEPARATOR);
        $files = array_diff(scandir($path), array('..', '.'));
        foreach ($files as $file) {
            $fPath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_file($fPath)) {
                if (!unlink($fPath)) $array[] = $fPath;
            } else mzFiles::recurringDelete($fPath, $array);
        }
        if (!rmdir($path)) $array[] = $path;
        return $array;
    }
    //====================================//
    public static function rename(string $path, string $name): mzRes
    {
        try {
            if (file_exists($path)) {
                $path = realpath($path);
                $rename = dirname($path) . DIRECTORY_SEPARATOR . $name;
                if (basename($name) !== $name) throw new Exception("name_cannot_be_path");
                if (rename($path, $rename)) return new mzRes(200, null, null, $rename);
                else throw new Exception("cannot_rename_path");
            }
            return new mzRes(400, null, "path_doesnot_exist", null);
        } catch (Exception $e) {
            return new mzRes(500, "rename_failed=$e");
        }
    }
    //====================================//
    public static function move(string $path, string $move_path, bool $replace = false): mzRes
    {
        try {
            $move = null;
            if (file_exists($path) && is_dir($move_path)) {
                $path = realpath($path);
                $move = realpath($move_path) . DIRECTORY_SEPARATOR . basename($path);
                if ($replace == true && file_exists($move)) mzFiles::delete($move);
                if (rename($path, $move)) {
                    mzFiles::delete($path);
                    return new mzRes(200, null, null, $move);
                } else throw new Exception("cannot_rename_path");
            }
            return new mzRes(400, null, "path_doesnot_exist", null);
        } catch (Exception $e) {
            if ($move !== null && file_exists($move)) mzFiles::delete($move);
            return new mzRes(500, "move_failed=$e");
        }
    }
    //====================================//
    public static function copy(string $path, string $copy_path, bool $replace = false): mzRes
    {
        try {
            $copy = null;
            if (file_exists($path) && file_exists($copy_path)) {
                $path = realpath($path);
                $copy = realpath($copy_path) . DIRECTORY_SEPARATOR . basename($path) . DIRECTORY_SEPARATOR;
                if ($replace == true && file_exists($copy)) mzFiles::delete($copy);
                if (is_file($path)) {
                    if (copy($path, $copy_path)) return new mzRes(200, null, null, $copy);
                    throw new Exception("cannot_copy_file ('$path')");
                }
                if (is_dir($path)) {
                    $res = mzFiles::scan($path, true);
                    if ($res->status != 200) return $res;
                    $array = $res->data;
                    foreach ($array as $k => $dir) {
                        if ($dir['type'] == 'dir') {
                            $nPath = substr_replace($dir['path'], $copy, 0, strlen($path));
                            if (is_dir($nPath) || mkdir($nPath, 0777, true)) unset($array[$k]);
                        }
                    }
                    foreach ($array as $k => $file) {
                        if ($file['type'] != 'dir') {
                            $nPath = substr_replace($file['path'], $copy, 0, strlen($path));
                            if (copy($file['path'], $nPath)) unset($array[$k]);
                        }
                    }
                    if (empty($array)) return new mzRes(200, null, null, $copy);
                    throw new Exception("cannot_copy_files ('" . implode("','", array_keys($array)) . "')");
                }
            }
            throw new Exception("path_doesnot_exist");
        } catch (Exception $e) {
            if ($copy !== null && file_exists($copy)) mzFiles::delete($copy);
            return new mzRes(500, "copy_failed=$e");
        }
    }
    //====================================//
    public static function compress(string $path, string $output_path = "", string $type = "application/zip"): mzRes
    {
        try {
            if (!is_dir($path)) throw new Exception("path_doesnot_exist");
            $path = realpath($path);
            $dir = dirname($path);
            $base = basename($path);
            $new = false;
            if (empty($output_path)) $output_path = $dir;
            if (!is_dir($output_path)) $new = true;
            if (!is_dir($output_path) && !mkdir($output_path, 0777, true)) throw new Exception("output_path_doesnot_exist");
            $output_path = rtrim($output_path, DIRECTORY_SEPARATOR);
            $output = realpath($output_path) . DIRECTORY_SEPARATOR . $base;
            //
            if ($type == "application/zip" && class_exists("ZipArchive")) {
                $archive = new ZipArchive();
                if ($archive->open("$output.zip", ZipArchive::CREATE) == false) throw new Exception("cannot_compress_path");
                //
                $res = mzFiles::scan($path, true);
                if ($res->status != 200) return $res;
                $files = $res->data;
                //
                foreach ($files as  $file) {
                    if ($file['type'] != "dir") $archive->addFile($file['path'], $base . substr($file['path'], strlen($path)));
                }
                //
                if ($archive->close() == false) throw new Exception("cannot_compress_path");
                return new mzRes(200, null, null, ["$output.zip"]);
            }
            //
            throw new Exception("file_type_unsupported");
        } catch (Exception $e) {
            if ($new == true) mzFiles::delete($output_path);
            return new mzRes(500, "compression_failed=$e");
        }
    }
    //====================================//
    public static function extract(string $path, string $output_path = "", bool $replace = false): mzRes
    {
        if (!is_file($path)) return new mzRes(400, null, "path_doesnot_exist");
        try {
            $path = realpath($path);
            $file = mzFiles::fileDetails($path);
            $new = false;
            if (empty($output_path)) $output_path = $path;
            if (!is_dir($output_path)) $new = true;
            if (!is_dir($output_path) && !mkdir($output_path, 0777, true)) throw new Exception("output_path_doesnot_exist");
            $output_path = rtrim($output_path, DIRECTORY_SEPARATOR);
            $output = realpath($output_path);
            //
            if ($replace == true && file_exists($output)) mzFiles::delete($output);
            if ($file['mime'] == "application/zip" && class_exists("ZipArchive")) {
                $archive = new ZipArchive();
                if ($archive->open($path) == false) throw new Exception("cannot_extract_file");
                $archive->extractTo($output);
                $archive->close();
                return new mzRes(200, null, null, $output);
            }
            if ($file['mime'] == "application/x-rar" && class_exists("RarArchive")) {
                $archive = RarArchive::open($path);
                $entries = $archive->getEntries();
                foreach ($entries as $entry) $entry->extract($output_path);
                $archive->close();
                return new mzRes(200, null, null, $output);
            }
            //
            if ($new == true) rmdir($output_path);
            throw new Exception("file_type_unsupported");
        } catch (Exception $e) {
            if ($new == true) mzFiles::delete($output_path);
            return new mzRes(500, "extraction_failed=$e");
        }
    }
}
