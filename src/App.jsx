import React from 'react';
import { Tabs, Tab, Box } from '@mui/material';

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
      {value === 0 && <Box>General Settings</Box>}
      {value === 1 && <Box>Advanced Settings</Box>}
    </Box>
  );
}

export default App;
