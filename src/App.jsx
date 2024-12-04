import React from 'react';
import { Tabs, Tab, Box } from '@mui/material';
import GeneralSettings from './components/GeneralSettings';
import AdvancedSettings from './components/AdvancedSettings';

function App() {
  const [value, setValue] = React.useState(0);

  const handleChange = (event, newValue) => {
    setValue(newValue);
  };

  return (
    <Box sx={{ width: '100%', typography: 'body1' }}>
      <Tabs value={value} onChange={handleChange} aria-label="Settings Tabs">
        <Tab label="General" />
        <Tab label="Advanced" />
      </Tabs>
      {value === 0 && <GeneralSettings />}
      {value === 1 && <AdvancedSettings />}
    </Box>
  );
}

export default App;
