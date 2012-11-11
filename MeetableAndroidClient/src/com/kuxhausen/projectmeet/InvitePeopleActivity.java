package com.kuxhausen.projectmeet;

import android.app.Activity;
import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.provider.BaseColumns;
import android.provider.ContactsContract;
import android.view.Menu;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.Toast;

public class InvitePeopleActivity extends Activity implements OnClickListener {
	
	Button addPerson;
	ListView addedPeople;

	Toast mToast;
    ResultDisplayer mPendingResult;
	class ResultDisplayer implements OnClickListener {
        String mMsg;
        String mMimeType;
        
        ResultDisplayer(String msg, String mimeType) {
            mMsg = msg;
            mMimeType = mimeType;
        }
        
        public void onClick(View v) {
            Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
            intent.setType(mMimeType);
            mPendingResult = this;
            startActivityForResult(intent, 1);
        }
    }
	
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_invitepeople);
		
		addedPeople = ((ListView)findViewById(R.id.addedPeopleListView));
		//addedPeople.setAdapter(adapter);
		
		// Watch for button clicks.
       Button addPerson =  ((Button)findViewById(R.id.addPersonButton));
       addPerson.setOnClickListener( new ResultDisplayer("Selected contact", ContactsContract.Contacts.CONTENT_ITEM_TYPE));
	}


	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		
		}
	}
	
	 @Override
	    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
	        if (data != null) {
	            Uri uri = data.getData();
	            if (uri != null) {
	                Cursor c = null;
	                try {
	                    c = getContentResolver().query(uri, new String[] { BaseColumns._ID },
	                            null, null, null);
	                    if (c != null && c.moveToFirst()) {
	                        int id = c.getInt(0);
	                        if (mToast != null) {
	                            mToast.cancel();
	                        }
	                        String txt = mPendingResult.mMsg + ":\n" + uri + "\nid: " + id;
	                        mToast = Toast.makeText(this, txt, Toast.LENGTH_LONG);
	                        mToast.show();
	                    }
	                } finally {
	                    if (c != null) {
	                        c.close();
	                    }
	                }
	            }
	        }
	    }
}
