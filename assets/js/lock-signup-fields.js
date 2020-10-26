var wpAuth0LockGlobalFields = [
  {
    name: "full_name",
    placeholder: "your full name",
    validator: function(address) {
      return {
        valid: address.length >= 10,
        hint: "Must have 10 or more chars" // optional
      };
    }
  }
];
