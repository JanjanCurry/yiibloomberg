<?php
$criteria = new CDbCriteria();
$criteria->compare('userId', $this->user->id);

$criteria2 = clone $criteria;
$criteria2->compare('status', 1);
$unseenCount = DbMessage::model()->count($criteria2);

$criteria2 = clone $criteria;
$criteria2->compare('status', '<=2');
$unreadCount = DbMessage::model()->count($criteria2);

$criteria->limit = 5;
$criteria->order = 'created DESC';
$alerts = DbMessage::model()->findAll($criteria);;
?>

<a href="#" class="dropdown-toggle <?php echo (!empty($unseenCount) ? 'unread' : ''); ?>" data-toggle="dropdown" role="button"><i class="fa fa-bell"></i><?php echo (!empty($unreadCount) ? ' ('.$unreadCount.')' : ''); ?></a>
<ul class="dropdown-menu">
    <?php
    if (!empty($alerts)) {
        foreach ($alerts as $alert) {
            $message = $alert->subject;
            if(empty($message)) {
                $message = $alert->message;
                if (strlen($message) > 25) {
                    $message = substr($message, 0, 25) . '&hellip;';
                }
            }

            switch ($alert->status) {
                case 1:
                    $icon = '<i class="fa fa-circle text-accent"></i>';
                    break;

                case 2:
                    $icon = '<i class="fa fa-circle"></i>';
                    break;

                case 3:
                default:
                    $icon = '<i class="fa fa-circle-o"></i>';
                    break;
            }

            echo '<li><a href="#" class="user-alert-item" data-id="' . $alert->id . '">' . $icon . ' ' . $message . '</a></li>';
        }

        echo '<li>'.CHtml::link('More Notifications', ['alert/index'], ['class' => 'text-center']).'</li>';
    } else {
        echo '<li><a href="#" class="text-center">No Notifications</a></li>';
    }
    ?>
</ul>
