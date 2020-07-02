<div class="content-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>Error Log</h3>
            </div>
            <div class="col-sm-6 text-right">
                <?php $form=$this->beginWidget('CActiveForm', array(
                    'method'=>'post',
                    'htmlOptions' => array('class' => 'deleteConfirmForm'),
                )); ?>
                <input type="hidden" name="clearLog" value="1" />
                <div class="form-group">
                    <button type="submit" class="btn btn-danger" aria-label="Clear log"><i class="fa fa-trash"></i> Clear Log</button>
                    <div class="clearfix"></div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php
    $data = array();

    if(!empty($log)){
        $errors = preg_split('/\\n---\\n/',$log, -1, PREG_SPLIT_NO_EMPTY);
        if(!empty($errors)){
            foreach($errors as $i => $error){
                $valid = true;
                $time = $user = $message = $trace = $url = '';

                //Time and date the error occurred
                preg_match("/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}?/",$error, $temp);
                if(!empty($temp)){
                    $time = $temp[0];
                }

                preg_match("/^[0-9]{4}\/[0-9]{2}\/[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}\s(.+)\n?/",$error, $temp);
                if(!empty($temp)){
                    $message = $temp[1];
                }

                preg_match("/\\nStack trace:\\n(.+?)?REQUEST_URI=/s",$error, $temp);
                if(!empty($temp)){
                    $trace = $temp[1];
                }

                preg_match("/REQUEST_URI\=(.+?)(\n|$)/s",$error, $temp);
                if(!empty($temp)){
                    $url = $temp[1];
                }

                if(strpos($message,'INSERT INTO YiiCache')){
                    $valid = false;
                }

                if($valid) {
                    $data[] = array(
                        'time' => $time,
                        //'user' => $user,
                        'message' => $message,
                        'trace' => $trace,
                        'url' => $url,
                    );
                }
            }
            rsort($data);
        }
    }

    if(!empty($data)){
        foreach($data as $i => $error){
            echo '
            <div class="alert alert-danger">
                <h4 class="clickable" data-toggle="collapse" data-target="#error_'.$i.'">'.$error["message"].'</h4>
                <p class="small">'.$error["time"].'<br />'.$error["url"].'</p>
                <p class="collapse" id="error_'.$i.'">'.nl2br($error["trace"]).'</p>
            </div>
            ';
        }
    }
    ?>
</div>
