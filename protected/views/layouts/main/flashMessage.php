<div id="notifications" class="hidden">
    <div class="notifications-overlay"></div>
    <div class="notifications-container">
        <?php foreach(Yii::app()->user->getFlashes() as $type => $messages) { ?>
            <div class="alert alert-<?php echo $type; ?>">
                <ul>
                    <?php
                        $newMessage = "";
                        if(is_array($messages)){
                            foreach($messages as $message){
                                $newMessage .= $message."<br />";
                            }
                        }else{
                            $newMessage = $messages;
                        }
                        echo '<li>'.$newMessage.'</li>';
                    ?>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>