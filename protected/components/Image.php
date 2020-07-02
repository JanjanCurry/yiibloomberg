<?php class Image extends CComponent {

    public $bgColor = 'F1F1F1';

    private $filename;
    private $image;

    public function createImage ($base64, $options = null) {
        $default = array(
            'filenameData' => null, //array
            'legend' => null, //base64 image
            'notes' => null, //string/array
            'source' => null, //string/array
            'title' => null, //string/array
        );
        $options = Yii::app()->format->options($options, $default);

        $valid = false;
        $this->generateFilename($options['filenameData']);

        if ($this->writeBase64Png($base64)) {
            $this->image = imagecreatefrompng($this->file);
            //$this->setBgColor($this->image);
            $this->addWatermark();
            $this->addLegend($options['legend']);
            if($options['title']) {
                $this->addText($options['title'], 'title');
            }
            if($options['notes']) {
                $this->addText($options['notes'], 'note');
            }
            if($options['source']) {
                $this->addText('Source(s): '.$options['source'], 'source');
            }
            imagedestroy($this->image);
            if (file_exists($this->file)) {
                $valid = true;
            }
        }

        return $valid;
    }

    public function createMultiImage ($base64s, $options = null) {
        $default = array(
            'filenameData' => null, //array
            'legend' => null, //base64 image
            'notes' => null, //string/array
            'source' => null, //string/array
            'title' => null, //string/array
        );
        $options = Yii::app()->format->options($options, $default);

        $valid = false;
        $this->generateFilename($options['filenameData']);

        foreach ($base64s as $i => $base64) {
            $title = null;
            if (!empty($options['title']) && !empty($options['title'][$i])) {
                $title = $options['title'][$i];
            }

            $note = null;
            if (!empty($options['notes']) && !empty($options['notes'][$i])) {
                $note = $options['notes'][$i];
            }

            $source = null;
            if (!empty($options['source']) && !empty($options['source'][$i])) {
                $source = 'Source(s): '.$options['source'][$i];
            }

            $legend = null;
            if (!empty($options['legend']) && !empty($options['legend'][$i])) {
                $legend = $options['legend'][$i];
            }

            if (!file_exists($this->file)) {
                //create first image
                if ($this->writeBase64Png($base64)) {
                    $this->image = imagecreatefrompng($this->file);
                    $this->addWatermark();
                    $this->addLegend($legend);
                    $this->addText($title, 'title');
                    $this->addText($note, 'note');
                    $this->addText($source, 'source');
                }
            } else {
                //merge other images
                $this->mergeImage($base64, array(
                    'title' => $title,
                    'note' => $note,
                    'legend' => $legend,
                ));
            }
        }
        imagedestroy($this->image);

        if (file_exists($this->file)) {
            $valid = true;
        }

        return $valid;
    }

    public function deleteImage () {
        $valid = false;
        if (file_exists($this->file)) {
            unlink($this->file);
            if (!file_exists($this->file)) {
                $valid = true;
            }
        }

        return $valid;
    }

    public function getDir () {
        return YiiBase::getPathOfAlias('root') . $this->subDir;
    }

    public function getSubDir () {
        return '/images/charts/';
    }

    public function getFile () {
        return $this->dir . $this->filename;
    }

    public function getFilename () {
        return $this->filename;
    }

    public function getUrl () {
        return YiiBase::getPathOfAlias('website') . $this->subDir . $this->filename;
    }

    public function getUrlRelative () {
        return $this->subDir . $this->filename;
    }

    private function addLegend ($base64) {
        if(!empty($base64)) {
            $legend = imagecreatefrompng($base64);

            if(!empty($legend) && is_resource($legend) && get_resource_type($legend) == 'gd') {

                //get dimensions
                $img1Width = imagesx($legend);
                $img1Height = imagesy($legend);

                $img2Width = imagesx($this->image);
                $img2Height = imagesy($this->image);

//                imagecopy($this->image, $legend, ($img2Width - $img1Width) / 2, 45, 0, 0, $img1Width, $img1Height);
//                $this->updateImage();


                //set new dimensions
                $mergedHeight = $img1Height + $img2Height - 20;
                $mergedWidth = $img1Width > $img2Width ? $img1Width : $img2Width;

                //create new blank image
                $mergedImage = imagecreatetruecolor($mergedWidth, $mergedHeight);
                //imagealphablending($mergedImage, false);
                //imagesavealpha($mergedImage, true);

                //set bg color
                $rgb = Yii::app()->format->hex2rgb($this->bgColor);
                $color = imagecolorallocate($mergedImage, $rgb['r'], $rgb['g'], $rgb['b']);
                //$white = imagecolorallocate($mergedImage, 255,255,255);
                imagefill($mergedImage, 0, 0, $color);

                //copy images to new blank image
                imagecopy($mergedImage, $this->image, 0, $img1Height-5, 0, 0, $img2Width, $img2Height);
                imagecopy($mergedImage, $legend, 0, 70, 0, 0, $img1Width, $img1Height);

                //release memory
                imagedestroy($this->image);
                imagedestroy($legend);

                //save image
                $this->image = $mergedImage;
                $this->updateImage();
            }
        }
    }

    private function addWatermark () {
        $watermark = imagecreatefrompng(YiiBase::getPathOfAlias('root') . '/images/watermark.png');

        $wmWidth = imagesx($watermark);
        $wmHeight = imagesy($watermark);

        $imWidth = imagesx($this->image);
        $imHeight = imagesy($this->image);

        imagecopy($this->image, $watermark, ($imWidth - $wmWidth) / 2, ($imHeight - $wmHeight) / 2, 0, 0, $wmWidth, $wmHeight);
        $this->updateImage();
    }

    private function addText ($strings, $type = 'title') {
        if (!empty($strings)) {
            if (!is_array($strings)) {
                $strings = array($strings);
            }

            //font settings
            switch ($type) {
                default:
                case 'note':
                    $font = YiiBase::getPathOfAlias('root') . '/css/font/source-sans-pro-regular.ttf';
                    $fontSize = 8;
                    $lineSpacing = 4;
                    break;

                case 'source':
                    $font = YiiBase::getPathOfAlias('root') . '/css/font/source-sans-pro-regular.ttf';
                    $fontSize = 8;
                    $lineSpacing = 4;
                    break;

                case 'title':
                    $font = YiiBase::getPathOfAlias('root') . '/css/font/montserrat-regular.ttf';
                    $fontSize = 12;
                    $lineSpacing = 8;
                    break;
            }

            $padding = 40;
            $black = imagecolorallocate($this->image, 0, 0, 0);
            $imgWidth = imagesx($this->image);

            $textHeight = 0;
            $textWidth = 0;
            $lines = array();
            foreach ($strings as $string) {
                $tempNote = $string;
                $i = 0;
                while (!empty($tempNote) && $i < 1000) {
                    //reduce text by 1 char
                    $charCount = strlen($tempNote) - $i;
                    $wrapped = explode('|', wordwrap($tempNote, $charCount, '|', true));
                    $line = $wrapped[0];

                    //get text width of current line
                    list($left, $bottom, $right, , , , $top) = imageftbbox($fontSize, 0, $font, $line);
                    $lineWidth = $right - $left + ($padding * 2);

                    if($lineWidth > $textWidth){
                        $textWidth = $lineWidth;
                    }

                    if ($lineWidth < $imgWidth) {
                        //this line of text will now fit, reset, remove processed line and run again for remaining lines
                        $lines[] = $line;
                        $textHeight = $bottom - $top + $lineSpacing + $textHeight;
                        $i = 0;
                        $tempNote = trim(substr($tempNote, strlen($line), strlen($tempNote)));
                    } else {
                        //text is still too wide, remove another char and run again
                        $i++;
                    }
                }
            }

            //write each line to the image
            if (!empty($lines)) {
                $rgb = Yii::app()->format->hex2rgb($this->bgColor);

                //create blank image for just the text
                $textHeight += $padding * 2;
                $textImage = imagecreatetruecolor($imgWidth, $textHeight);
                //$textImage = $this->setBgColor($textImage);
                //$white = imagecolorallocate($textImage, 255,255,255);
                $color = imagecolorallocate($textImage, $rgb['r'], $rgb['g'], $rgb['b']);
                imagefill($textImage, 0, 0, $color);

                $textBg = imagecreatetruecolor($imgWidth, $textHeight);
                $color = imagecolorallocate($textBg, $rgb['r'], $rgb['g'], $rgb['b']);
                imagefill($textBg, 0, 0, $color);

                //add text to the new blank image
                $y = $padding;
                foreach ($lines as $i => $line) {
                    if ($i > 0) {
                        $y += $fontSize + $lineSpacing;
                    }
                    imagettftext($textImage, $fontSize, 0, $padding, $y, $black, $font, $line);
                }

                //get dimensions
                list($img1Width, $img1Height) = getimagesize($this->file);
                list($img2Width, $img2Height) = array($imgWidth, $textHeight);

                //set new dimensions
                switch ($type) {
                    default:
                    case 'note':
                    case 'source':
                        //$img1Height -= $padding;
                        break;

                    case 'title':
                        $img2Height -= $padding;
                        $img1Height -= $padding;
                        break;
                }

                $mergedHeight = $img1Height + $img2Height;
                $mergedWidth = $img1Width > $img2Width ? $img1Width : $img2Width;

                //create new blank image
                $mergedImage = imagecreatetruecolor($mergedWidth, $mergedHeight);
                imagealphablending($mergedImage, false);
                imagesavealpha($mergedImage, true);

                //copy images to new blank image
                switch ($type) {
                    default:
                    case 'note':
                        imagecopy($mergedImage, $this->image, 0, 0, 0, 0, $img1Width, $img1Height);
                        imagecopy($mergedImage, $textImage, 0, $img1Height, 0, 0, $img2Width, $img2Height);
                        break;

                    case 'source':
                        imagecopy($mergedImage, $this->image, 0, 0, 0, 0, $img1Width, $img1Height);
                        imagecopy($mergedImage, $textBg, 0, $img1Height, 0, 0, $img2Width, $img2Height);
                        imagecopy($mergedImage, $textImage, $img2Width/2 - $textWidth/2, $img1Height, 0, 0, $img2Width, $img2Height);
                        break;

                    case 'title':
                        imagecopy($mergedImage, $textImage, 0, 0, 0, 0, $img2Width, $img2Height);
                        $this->image = imagecrop($this->image, array('x' => 0, 'y' => $padding, 'width' => $img1Width, 'height' => $img1Height));
                        imagecopy($mergedImage, $this->image, 0, $img2Height, 0, 0, $img1Width, $img1Height);
                        break;
                }

                //release memory
                imagedestroy($this->image);
                imagedestroy($textImage);

                //save image
                $this->image = $mergedImage;
                $this->updateImage();
            }
        }
    }

    private function generateFilename ($filenameData) {
        $filename = '';
        $exists = true;
        $i = 0;
        while ($exists == true && $i < 1000) {
            $append = '';
            if ($i > 0) {
                $append = '-' . $i;
            }

            $filename = 'CompleteIntelligence';
            if (!empty($filenameData)) {
                foreach ($filenameData as $val) {
                    if (!empty($val)) {
                        $filename .= '-' . $val;
                    }
                }
            }
            $filename .= '-' . date('YmdHi');
            $filename .= $append . '.png';

            if (!file_exists($this->dir . $filename)) {
                $exists = false;
            }
            $i++;
        }

        $this->filename = $filename;
    }

    private function mergeImage ($base64, $options = null) {
        $default = array(
            'legend' => null, //base64 image
            'notes' => null, //string/array
            'title' => null, //string/array
        );
        $options = Yii::app()->format->options($options, $default);

        $tmpFilename = substr(base64_encode(md5(time() . rand(111111, 999999))), 0, 40) . '.png';
        $img = file_get_contents($base64);
        if ($img !== false) {
            header('Content-Type: image/png');
            if (file_put_contents($this->dir . $tmpFilename, $img)) {
                if (file_exists($this->dir . $tmpFilename)) {
                    $mainFilename = $this->filename;
                    $mainImage = $this->image;

                    //create temp image
                    $this->filename = $tmpFilename;
                    $this->image = imagecreatefrompng($this->dir . $tmpFilename);
                    $this->addWatermark();
                    $this->addLegend($options['legend']);
                    $this->addText($options['title'], 'title');
                    $this->addText($options['notes'], 'note');

                    //get dimensions
                    list($img1Width, $img1Height) = getimagesize($this->dir . $mainFilename);
                    list($img2Width, $img2Height) = getimagesize($this->file);

                    //set new dimensions
                    $mergedHeight = $img1Height + $img2Height;
                    $mergedWidth = $img1Width > $img2Width ? $img1Width : $img2Width;

                    //create new blank image
                    $mergedImage = imagecreatetruecolor($mergedWidth, $mergedHeight);
                    imagealphablending($mergedImage, false);
                    imagesavealpha($mergedImage, true);

                    //copy images to new blank image
                    imagecopy($mergedImage, $mainImage, 0, 0, 0, 0, $img1Width, $img1Height);
                    imagecopy($mergedImage, $this->image, 0, $img1Height, 0, 0, $img2Width, $img2Height);

                    //release memory
                    imagedestroy($this->image);
                    imagedestroy($mainImage);
                    unlink($this->file);

                    //save image
                    $this->image = $mergedImage;
                    $this->filename = $mainFilename;
                    $this->updateImage();
                }
            }
        }
    }

    private function setBgColor($im, $color = null){
        $color = (!empty($color) ? $color : $this->bgColor);
        //list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
        $rgb = Yii::app()->format->hex2rgb($color);

        $imWidth = imagesx($this->image);
        $imHeight = imagesy($this->image);

        $mergedImage = imagecreatetruecolor($imWidth, $imHeight);
        $color = imagecolorallocate($mergedImage, $rgb['r'], $rgb['g'], $rgb['b']);
        imagefill($mergedImage, 0, 0, $color);
        imagealphablending($mergedImage, false);
        imagesavealpha($mergedImage, true);

        imagecopy($mergedImage, $this->image, 0, 0, 0, 0, $imWidth, $imHeight);
        $this->image = $mergedImage;

        $this->updateImage();
        return $im;
    }

    private function updateImage () {
        header('Content-type: image/png');
        imagepng($this->image, $this->file);
    }

    private function writeBase64Png ($base64) {
        $valid = false;
        $img = file_get_contents($base64);
        if ($img !== false) {
            header('Content-Type: image/png');
            if (file_put_contents($this->file, $img)) {
                if (file_exists($this->file)) {
                    $valid = true;
                }
            }
        }

        return $valid;
    }

}