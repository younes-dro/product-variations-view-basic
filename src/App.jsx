import React from 'react';
import { Tabs, Tab, Box } from '@mui/material';
import GeneralSettings from './components/GeneralSettings';


function App() {
  const [value, setValue] = React.useState(0);

  const handleChange = (event, newValue) => {
    setValue(newValue);
  };

  return (
    <Box sx={{ width: '100%', typography: 'body1' }}>
      <Tabs value={value} onChange={handleChange} aria-label="Settings Tabs">
        <Tab label="General" />  
      </Tabs>
      {value === 0 && <GeneralSettings />}
    </Box>
  );
}

export default App;
