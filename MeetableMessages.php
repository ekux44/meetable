<?php

/*
	Available variables
	{CREATOR_NAME}
	{USER_NAME}
	{MEETING_NAME}
	{MEETING_RANGE}
	{MEETING_LENGTH}
*/
class MeetableMessages
{
static $smsMessages = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you. What time or time range can you meet for {MEETING_LENGTH} {MEETING_RANGE}? (HELP for more info)',
'confirm_time' => 'You have selected the time {USER_RANGE}. To change this please reply with another time or time range',
'bad-input' => 'Sorry, we did not understand your response. Please try again.'
);

static $emailMessages = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you. What time or time range can you meet for {MEETING_LENGTH} {MEETING_RANGE}? (HELP for more info)',
'confirm_time' => 'You have selected the time {USER_RANGE}. To change this please reply with another time or time range',
'bad-input' => 'Sorry, we did not understand your response. Please try again.'
);

static $emailSubjects = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you',
'confirm_time' => 'Confirmation of your time for {MEETING_NAME}',
'bad-input' => 'We had trouble understanding your reply.'
);
}