<?
$MESS ["VBCHBB_EVENT_TYPE_NAME"] = "Bonus system. Loyalty programs - templates";
$MESS ["VBCHBB_EVENT_TYPE_DESC"] = "# DESCRIPTION # - Description
# MESSAGE # - from profile template
# ACTIVE_FROM # - Active with
# ACTIVE_TO # - Active until
# BONUS # - Added bonuses
# BONUS_ALL # - Total Bonuses
# USERNAME # - First name, last name or username
# USEREMAIL # - EMail user ";
$MESS ["VBCHBB_EVENT_SUBJECT_ADD"] = "# SITE_NAME #: bonuses have been added to you";
$MESS ["VBCHBB_EVENT_SUBJECT_DELETE"] = "# SITE_NAME #: Your bonuses have been deducted";
$MESS ["VBCHBB_EVENT_SUBJECT_PAY"] = "# SITE_NAME #: You paid for the order with bonuses";
$MESS ["VBCHBB_EVENT_SUBJECT_STATISTIC"] = "# SITE_NAME #: Statistics on bonus account";
$MESS ["VBCHBB_EVENT_SUBJECT_PREDELETE"] = "# SITE_NAME #: In # DAY # of the day, your bonuses will burn";
$MESS ["VBCH_SENDQUE_EVENT_BODY_PREDELETE"] = "
В В Hello # USERNAME #

Your bonuses will be burned at # SITE_NAME #

Bonuses will burn: # BONUS #
Activity from # ACTIVE_FROM # to # ACTIVE_TO #
Bonus balance: # BONUS_ALL #

-------------------------------------------------- ------------------------------------
letter generated automatically
В В ";

$MESS ["VBCH_SENDQUE_EVENT_BODY_ADD"] = "Hello # USERNAME #

You have added bonuses on the site # SITE_NAME #
# MESSAGE #
# DESCRIPTION #

Added bonuses: # BONUS #
Activity from # ACTIVE_FROM # to # ACTIVE_TO #
Total bonuses: # BONUS_ALL #


-------------------------------------------------- ------------------------------------
letter generated automatically
В В ";
$MESS ["VBCH_SENDQUE_EVENT_BODY_DELETE"] = "Hello, # USERNAME #

# SITE_NAME # bonuses are written off

# DESCRIPTION #

Bonuses written off: # BONUS #
Activity from # ACTIVE_FROM # to # ACTIVE_TO #
Total bonuses: # BONUS_ALL #


-------------------------------------------------- ------------------------------------
letter generated automatically
В В ";
$MESS ["VBCH_SENDQUE_EVENT_BODY_PAY"] = "Hello, # USERNAME #

You have paid part (or all) of the order on the site # SITE_NAME # bonuses

# DESCRIPTION #

Bonuses written off: # BONUS #
Bonuses left: # BONUS_ALL #


-------------------------------------------------- ------------------------------------
letter generated automatically
В В ";
$MESS ["VBCH_SENDQUE_EVENT_BODY_STATISTIC"] = "Hello, # USERNAME #

Statistics of the bonus program:

# DESCRIPTION #

Total bonuses: # BONUS_ALL #


-------------------------------------------------- ------------------------------------
letter generated automatically
В В ";
$MESS ['ITR_VBCH_ERROR_FILTER'] = 'Error checking profile filter';
$MESS ['VBCHBB_SALE_NOT_VERSION'] = 'The version of the sale module is less than 16.5.0';
$MESS ['VBCHBB_ALLBONUS_ADD'] = 'You have added bonuses';
$MESS ['VBCHBB_ALLBONUS_DELETE'] = 'Your bonuses have been deducted';