package com.kuxhausen.projectmeet;

import java.util.Calendar;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.TimePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.TimePicker;

public class NewMeetingActivity extends Activity implements OnClickListener{
	
	Button addPeople, setDay, setDuration;
	RadioGroup contactMethod;
	TextView dayDisplay, durationDisplay;

    // date and time
    private int mYear;
    private int mMonth;
    private int mDay;
    private int mHour;
    private int mMinute;

    static final int TIME_DIALOG_ID = 0;
    static final int DATE_DIALOG_ID = 1;
	
	@Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_newmeeting);
        
        
        
        dayDisplay = (TextView) findViewById(R.id.dayTextView);
        durationDisplay = (TextView) findViewById(R.id.durationTextView);

        setDay = (Button) findViewById(R.id.dayButton);
        setDay.setOnClickListener(this);
        
        setDuration = (Button) findViewById(R.id.durationButton);
        setDuration.setOnClickListener(this);
        
        addPeople = (Button) findViewById(R.id.addPeopleButton);
        addPeople.setOnClickListener(this);
        

        final Calendar c = Calendar.getInstance();
        mYear = c.get(Calendar.YEAR);
        mMonth = c.get(Calendar.MONTH);
        mDay = c.get(Calendar.DAY_OF_MONTH);
        mHour = c.get(Calendar.HOUR_OF_DAY);
        mMinute = c.get(Calendar.MINUTE);

        updateDisplay();
    }
	
	
	
	
	@Override
    protected Dialog onCreateDialog(int id) {
        switch (id) {
            case TIME_DIALOG_ID:
                return new TimePickerDialog(this,
                        mTimeSetListener, mHour, mMinute, false);
            case DATE_DIALOG_ID:
                return new DatePickerDialog(this,
                            mDateSetListener,
                            mYear, mMonth, mDay);
        }
        return null;
    }

    @Override
    protected void onPrepareDialog(int id, Dialog dialog) {
        switch (id) {
            case TIME_DIALOG_ID:
                ((TimePickerDialog) dialog).updateTime(mHour, mMinute);
                break;
            case DATE_DIALOG_ID:
                ((DatePickerDialog) dialog).updateDate(mYear, mMonth, mDay);
                break;
        }
    }    

    private void updateDisplay() {
        dayDisplay.setText(
            new StringBuilder()
                    // Month is 0 based so add 1
                    .append(mMonth + 1).append("-")
                    .append(mDay).append("-")
                    .append(mYear).append(" "));
        
        
        durationDisplay.setText(
                new StringBuilder()
                        .append(pad(mHour)).append(":")
                        .append(pad(mMinute)));
    }

    private DatePickerDialog.OnDateSetListener mDateSetListener =
            new DatePickerDialog.OnDateSetListener() {

                public void onDateSet(DatePicker view, int year, int monthOfYear,
                        int dayOfMonth) {
                    mYear = year;
                    mMonth = monthOfYear;
                    mDay = dayOfMonth;
                    updateDisplay();
                }
            };

    private TimePickerDialog.OnTimeSetListener mTimeSetListener =
            new TimePickerDialog.OnTimeSetListener() {

                public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
                    mHour = hourOfDay;
                    mMinute = minute;
                    updateDisplay();
                }
            };

    private static String pad(int c) {
        if (c >= 10)
            return String.valueOf(c);
        else
            return "0" + String.valueOf(c);
    }




	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.addPeopleButton:
			startActivity(new Intent(this, MainActivity.class));
			break;
		case R.id.durationButton:	
			showDialog(TIME_DIALOG_ID);
			break;
		case R.id.dayButton:
			showDialog(DATE_DIALOG_ID);
			break;
		}
		
	}
}
